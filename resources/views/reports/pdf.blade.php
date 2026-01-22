<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Daily Reports Export</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .meta {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Report Export</h2>
        <p>Generated on {{ date('d M Y H:i') }}</p>
    </div>

    @foreach($reports as $report)
        <div style="page-break-inside: avoid; margin-bottom: 30px;">
            <div class="meta">
                <strong>Date:</strong> {{ $report->report_date->format('d M Y') }} <br>
                <strong>Supervisor:</strong> {{ $report->supervisor->name }} <br>
                <strong>Task:</strong> {{ $report->todoList->title }} <br>
                <strong>Session:</strong> {{ ucfirst($report->session ?? '-') }}
            </div>

            <!-- Man Power -->
            @if($report->manPower)
            <h4>Man Power</h4>
            <table>
                <tr>
                    <th>Employees Present</th>
                    <th>Employees Absent</th>
                </tr>
                <tr>
                    <td>{{ $report->manPower->employees_present }}</td>
                    <td>{{ $report->manPower->employees_absent }}</td>
                </tr>
            </table>
            @endif

            <!-- Warehouse Conditions -->
            @if($report->warehouseConditions->count() > 0)
            <h4>Warehouse Conditions</h4>
            <table>
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Condition</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->warehouseConditions as $condition)
                    <tr>
                        <td>{{ strtoupper($condition->warehouse) }}</td>
                        <td>
                            @if($condition->check_1) Sangat Bersih
                            @elseif($condition->check_2) Bersih
                            @elseif($condition->check_3) Cukup Bersih
                            @elseif($condition->check_4) Kurang Bersih
                            @elseif($condition->check_5) Tidak Bersih
                            @endif
                        </td>
                        <td>{{ $condition->notes }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            
            <hr>
        </div>
    @endforeach
</body>
</html>
