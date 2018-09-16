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
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css"
              href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">

        <!-- Styles -->

    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="text-center">
            </div>
            <div class="animationload">
                <div class="osahanloading"></div>
            </div>
        </div>
    </div>
            <?php if(!empty($reports)){
                ?>
            <div class="container">
                <div class="row"><a href="#" class="generate btn" style="color: cornflowerblue; font-size: 21px">Generate new report</a></div>
                <hr>
                <table id="table_id" class="display">
                <thead>
                <tr>
                    <th>Report Id</th>
                    <th>Report Type</th>
                    <th>Report Date Period</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reports as $report){ ?>
                    <tr>
                        <td><a href="/reportDetails?id=<?=$report['id']?>"><?=$report['name']?></a></td>
                        <td><a href="/reportDetails?id=<?=$report['id']?>">Campaign / SponsoredProducts</a></td>
                        <td><a href="/reportDetails?id=<?=$report['id']?>"><?=date("Y-m-d", strtotime($report['date_period']))?></a></td>
                        <td><a class="link" href="/reportDetails?id=<?=$report['id']?>">View Detailed</a> </td>
                    </tr>
               <?php }
                ?>

                </tbody>
            </table>
            </div>
            <?php }else{ ?>
                <div class="flex-center position-ref full-height">
                    <div class="content">
                    <div class="title m-b-md">
                        Welcome
                    </div>
                    <div class="links">
                        <a href="#" style="cursor: default;">There is no data to show currently , You can easly generate reports here</a>
                        <a href="#" class="generate" style="color: cornflowerblue; font-size: 21px">Generate</a>

                    </div>

                </div>
                </div>
        <?php    }?>

            <div class="modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Report Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Please select date period</p>
                            <select class="form-data" name="reportDate">
                                <option value="20180820">2018-08-20</option>
                                <option value="20180821">2018-08-21</option>
                                <option value="20180822">2018-08-22</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

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

<script>
    $('#table_id').DataTable();
</script>
