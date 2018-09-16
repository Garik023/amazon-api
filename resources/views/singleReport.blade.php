<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
    <!-- Styles -->

</head>
<body>

<?php if(!empty($reportData)){
?>
<div class="container">
    <table id="table_id" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th> Name</th>
            <th> Type</th>
            <th>Cost</th>
            <th>Campaign Id</th>
            <th>14d Same SKU</th>
            <th>Impressions</th>
            <th> Budget</th>
            <th>Conversions 14d</th>
            <th> Budget Type</th>
            <th> Status</th>
            <th>Clicks</th>
            <th>Placement</th>
            <th>Sales 14d</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reportData as $report){ ?>
        <tr>
            <td><?=$report['campaignName']?></td>
            <td><?=$report['campaignType']?></td>
            <td><?=$report['cost']?></td>
            <td><?=$report['campaignId']?></td>
            <td><?=$report['attributedSales14dSameSKU']?></td>
            <td><?=$report['impressions']?></td>
            <td><?=$report['campaignBudget']?></td>
            <td><?=$report['attributedConversions14d']?></td>
            <td><?=$report['campaignBudgetType']?></td>
            <td><?=$report['campaignStatus']?></td>
            <td><?=$report['clicks']?></td>
            <td><?=$report['placement']?></td>
            <td><?=$report['attributedSales14d']?></td>

        </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
<?php }
?>


</body>
</html>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>


<script src="{{ asset('js/scripts.js') }}"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="{{ asset('js/scripts.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#table_id').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });

</script>
