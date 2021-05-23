<?php

$mappings = (array) json_decode(file_get_contents('./Mapping/categories.json'));

$chart = array_fill_keys(
    array_keys(
        array_flip(
            array_filter(
                array_unique(
                    array_values($mappings)
                )
            )
        )
    )
    , 0);

$chart['Other'] = 0;

$files = glob("./Statements/*.csv");
$transactions = [];

foreach ($files as $file) {

    $name = explode('.', basename($file))[0];

    if (($handle = fopen($file, "r")) !== FALSE) {

        $row = 0;
        $keys = [];

        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {

            $data = array_splice($data, 0, 6);

            if (!$row++) {
                $keys = $data;
                continue;
            }

            $tx = (object) array_combine($keys, $data);

            // Pretty up the Details
            $tx->Details = trim(preg_replace('/\s\s+/', ' ', $tx->Details));
            $tx->User = $name;

            $tx->Category = '';
            foreach (array_keys($mappings) as $search) {

                if (stripos($tx->Details, $search) !== false) {

                    $tx->Category = $mappings[$search];

                    if ($tx->Type == 'D') {
                        $chart[$tx->Category] += $tx->Amount;
                    }

                    break;
                }
            }

            if (!$tx->Category) {
                $tx->Category = 'Other';
            }

            $transactions[] = $tx;
        }
        fclose($handle);
    }
    else {
        echo "Could not open file: " . $file;
        die;
    }
}

$credits = $debits = [];

// Sort TX by date (Suports date order for multiple cards)
uasort($transactions, function($tx1, $tx2) {
    return strtotime(str_replace('/', '-', $tx2->TransactionDate)) <=> strtotime(str_replace('/', '-', $tx1->TransactionDate));
});


foreach ($transactions as $tx) {

    switch ($tx->Type) {
        case 'C':
        $credits[] = $tx;
        break;

        case 'D':
        $debits[] = $tx;
        break;
    }
}

asort($chart);



?>

<!doctype html>
    <html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

        <title>My Accounts</title>
    </head>
    <body>
        <div class="container pb-5" style="max-width: 700px;" >
            <canvas id="myChart"></canvas>
        </div>



        <div class="container">
            <div class="row">
                <table class="table table-striped" id="dataTable">
                    <thead>
                        <tr>
                            <th scope="col">TX#</th>
                            <th scope="col">Card</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Category</th>
                            <th scope="col">Details</th>
                            <th scope="col">Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 1;
                        ?>

                        <?php foreach ($debits as $tx) { ?>
                            <tr>
                                <th scope="row"><?php echo $count++ ?></th>
                                <td><?php echo sprintf('%s %s', $tx->User, $tx->Card) ?></td>
                                <td><?php echo $tx->Amount ?></td>
                                <td><?php echo $tx->Category ?></td>
                                <td><?php echo $tx->Details ?></td>
                                <td><?php echo $tx->TransactionDate ?></td>

                            </tr>

                        <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js@3.2.1/dist/chart.min.js"></script>
        <script type="text/javascript">

            $(function() {
                $(document).ready( function () {
                    $('#dataTable').DataTable({
                        "pageLength": 50
                    });
                } );

                var labels = <?php echo json_encode(array_keys($chart)) ?>;

                var randomColorGenerator = function () {
                    let returnArray = [];
                    for (let i = 0; i < labels.length; i++) {
                        returnArray.push('#' + (Math.random().toString(16) + '0000000').slice(2, 8));
                    }
                    return returnArray
                };

                var ctx = document.getElementById('myChart').getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Spend",
                            backgroundColor: randomColorGenerator(),
                            data: <?php echo json_encode(array_values($chart)) ?>
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Accounts'
                            }
                        }
                    },
                });

            })
        </script>
    </body>
    </html>


    <?php

    function dd(...$args) {
        foreach ($args as $arg) {
            echo '<pre>';
            print_r($arg);
            echo '</pre>';
        }
        die;
    }
    function dump(...$args) {
        foreach ($args as $arg) {
            echo '<pre>';
            print_r($arg);
            echo '</pre>';
        }
    }


