<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://unpkg.com/bootstrap-table@1.17.1/dist/bootstrap-table.min.css" rel="stylesheet">
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-5">
                <div class="form-row">
                    <div class="col">
                        <input type="number" class="form-control" id="salary" value="25" placeholder="salary">
                    </div>
                    <button id="refreshSalary" class="btn btn-primary mb-2">refresh salary</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h1>HITUNG GAJI</h1>
                <div class="form-row">
                    <div class="col">
                        <input type="text" class="form-control" id="start" placeholder="Start Time">
                    </div>

                    <div class="col">
                        <input type="text" class="form-control" id="finish" placeholder="Finish Time">
                    </div>

                    <button id="addToTable" class="btn btn-primary mb-2">Add</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Start</th>
                            <th>Finish</th>
                            <th>Hourly</th>
                            <th>Total Hour</th>
                            <th>Salary</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">
                                <h3>Total Salary</h3>
                            </th>
                            <th colspan="2" id="total_salary" class="text-right">
                                <h3>0 AUD</h3>
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">
                                <h3>After Tax (15%)</h3>
                            </th>
                            <th colspan="2" id="tax_salary" class="text-right">
                                <h3>0 AUD</h3>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="assets/node_modules/moment/moment.js"></script>
    <script>
        $(document).ready(function() {
            $('#start').datetimepicker({
                format: 'dd mmmm yyyy HH:MM',
                footer: true,
                modal: true
            });

            $('#finish').datetimepicker({
                format: 'dd mmmm yyyy HH:MM',
                footer: true,
                modal: true
            });

            load_salary();
        });

        $('#refreshSalary').on('click', function() {
            let new_salary = $('#salary').val();
            let all_data = (function() {
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "http://localhost:3000/api/salary/",
                    success: function(data) {
                        $.each(data.response, function(i, data) {
                            let start = moment(data.start);
                            let finish = moment(data.finish);

                            let totalHour = finish.diff(start, 'minutes') / 60;
                            let hourly = (function(callback) {
                                let start = moment(data.start);
                                let day = start.get('day');

                                let salary = new_salary;
                                if (day == 6) salary = salary * 1.25;
                                if (day == 0) salary = salary * 1.5;
                                return salary;
                            })();
                            let totalSalary = totalHour * hourly;

                            let param = {
                                salaryID: data._id,
                                hourly: hourly,
                                total_hour: totalHour,
                                salary: totalSalary
                            }
                            updateSalary(param);
                            // console.log(param);
                        });
                        load_salary();
                    },
                    error(error) {
                        console.log(error);
                    }
                });
            })();
        });

        $('#addToTable').on('click', function() {
            let start = moment($('#start').val());
            let finish = moment($('#finish').val());
            // let totalHour = finish - start;
            let totalHour = finish.diff(start, 'minutes') / 60;

            let hourly = (function(callback) {
                let start = moment($('#start').val());
                let day = start.get('day');
                // console.log(day);
                let salary = 25;
                if (day == 6) salary = salary * 1.25;
                if (day == 0) salary = salary * 1.5;
                return salary;
            })();
            let totalSalary = totalHour * hourly;

            let param = {
                start: start._d,
                finish: finish._d,
                hourly: hourly,
                total_hour: totalHour,
                salary: totalSalary
            }

            // console.log(param);
            addSalary(param);
            // let row = `
            //         <tr>
            //             <td>${start}</td>
            //             <td>${finish}</td>
            //             <td>${hourly}</td>
            //             <td>${totalHour}</td>
            //             <td id="totalSalary">${totalSalary}</td>
            //             <td><button class="badge badge-danger" id="remove">x</button></td>
            //         </tr>
            // `;

            // $('tbody').append(row);
            // getTotalSalary();
        });

        $('tbody').on('click', '#remove', function() {
            let btn = $(this);
            let salaryID = btn.data('salaryid');
            deleteSalary(salaryID);
        })

        function getTotalSalary() {
            let total = 0;
            const tax = 15 / 100;
            $('tbody').children().each(function() {
                let salary = parseFloat($(this).find('#totalSalary').text());
                total += salary;
            });
            // console.log(total);
            $('#total_salary').html(`<h3>${total} AUD</h3>`);

            let tax_salary = total - (total * tax);
            $('#tax_salary').html(`<h3>${tax_salary} AUD</h3>`);
        }

        //    --------------- api area--------------

        function load_salary() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "https://iseng-salary-api.herokuapp.com/api/salary/",
                success: function(data) {
                    // console.log(data);
                    $('tbody').html('');
                    $.each(data.response, function(i, data) {
                        let row = `
                                <tr>
                                    <td>${data.start}</td>
                                    <td>${data.finish}</td>
                                    <td>${data.hourly}</td>
                                    <td>${data.total_hour}</td>
                                    <td id="totalSalary">${data.salary}</td>
                                    <td><button class="badge badge-danger" data-salaryid="${data._id}" id="remove">x</button></td>
                                </tr>
                        `;

                        $('tbody').append(row);
                        getTotalSalary();
                    })
                },
                error(error) {
                    console.log(error);
                }
            });
        };

        function addSalary(params) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "https://iseng-salary-api.herokuapp.com/api/salary/store",
                data: params,
                success: function(data) {
                    // console.log(data);
                    load_salary();
                },
                error(error) {
                    console.log(error);
                }
            });
        }

        function updateSalary(params) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "https://iseng-salary-api.herokuapp.com/api/salary/update",
                data: params,
                success: function(data) {
                    console.log(data);
                    // load_salary();
                },
                error(error) {
                    console.log(error);
                }
            });
        }

        function deleteSalary(salaryID) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "https://iseng-salary-api.herokuapp.com/api/salary/delete",
                data: {
                    salaryID: salaryID
                },
                success: function(data) {
                    // console.log(data);
                    load_salary();
                },
                error(error) {
                    console.log(error);
                }
            });
        }
    </script>
</body>

</html>