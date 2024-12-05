<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Attendance Records</h3>
        <div class="card-tools align-middle">
            <button class="btn btn-success btn-sm py-1 rounded-0" type="button" id="print"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover" id="att-list">
            <colgroup>
                <col width="5%">
                <col width="45%">
                <col width="25%">
                <col width="25%">
            </colgroup>
            <thead>
                <tr>
                    <th class="p-0 text-center">#</th>
                    <th class="p-0 text-center">Employee</th>
                    <th class="p-0 text-center">Attendance Type</th>
                    <th class="p-0 text-center">Attendance DateTime</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // Fetch attendance records for today
                $today = date("Y-m-d");
                $query = "
                    SELECT 
                        a.*, 
                        t.name AS tname, 
                        (e.lastname || ', ' || e.firstname || ' ' || e.middlename) AS fullname, 
                        e.employee_code 
                    FROM attendance_list a 
                    INNER JOIN employee_list e ON a.employee_id = e.employee_id 
                    INNER JOIN att_type_list t ON a.att_type_id = t.att_type_id 
                    WHERE date(a.date_created) = '$today'
                    ORDER BY a.date_created ASC
                ";

                $att_qry = $conn->query($query);

                if (!$att_qry) {
                    die("Query failed: " . $conn->lastErrorMsg());
                }

                $rows = [];
                while ($row = $att_qry->fetchArray(SQLITE3_ASSOC)) {
                    $rows[] = $row;
                }

                if (count($rows) == 0): 
                ?>
                <tr>
                    <td colspan="4" class="text-center">No attendance records found for today.</td>
                </tr>
                <?php else: 
                $i = 1;
                foreach ($rows as $row):
                    $bg = "primary";
                    if (in_array($row['att_type_id'], array(2, 4))) {
                        $bg = "danger";
                    }
                ?>
                <tr>
                    <td class="align-middle py-0 px-1 text-center"><?php echo $i++; ?></td>
                    <td class="align-middle py-0 px-1">
                        <p class="m-0">
                            <small><b>Employee Code:</b> <?php echo $row['employee_code']; ?></small><br>
                            <small><b>Name:</b> <?php echo $row['fullname']; ?></small>
                        </p>
                    </td>
                    <td class="align-middle py-0 px-1 text-center">
                        <span class="badge bg-<?php echo $bg; ?>"><?php echo $row['tname']; ?></span>
                    </td>
                    <td class="align-middle py-0 px-1 text-end"><?php echo date("M d, Y h:i A", strtotime($row['date_created'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#print').click(function(){
            var _h = $("head").clone();
            var _table = $('#att-list').clone();
            var _el = $("<div>");
            _el.append(_h);
            _el.append("<h2 class='text-center'>Attendance List</h2>");
            _el.append("<hr/>");
            _el.append(_table);

            var nw = window.open("", "_blank", "width=1200,height=900");
            nw.document.write(_el.html());
            nw.document.close();
            setTimeout(() => {
                nw.print();
                setTimeout(() => {
                    nw.close();
                }, 200);
            }, 200);
        });
    });
</script>
