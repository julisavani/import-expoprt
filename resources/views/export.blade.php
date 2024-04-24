<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}-{{ $date }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        table {
            border-left: 2px solid #ccc;
            border-right: 0;
            border-top: 2px solid #ccc;
            border-bottom: 0;
            border-collapse: collapse;
        }
        table td,
        table th {
            border-left: 0;
            border-right: 2px solid #ccc;
            border-top: 0;
            border-bottom: 2px solid #ccc;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $date }}</p>

    <table class="" width="100%" style="padding: 0 100px">
        <thead>
            <tr>
                <th>No.of.Pcs</th>
                <th>Weight</th>
                <th>RAP AVG</th>
                <th>RAP TOTAL</th>
                <th>AVG DIS%</th>
                <th>AVG P.CT</th>
                <th>TOTAL VL</th>
            </tr>

            @php
                $Weight = 0;
                $RapTotal = 0;
                $TotalVal = 0;
                foreach ($Products as $Product) {
                    $Weight = $Weight + $Product->carat;
                    $RapTotal = $RapTotal + $Product->rapo_amount;
                    $TotalVal = $TotalVal + $Product->amount;
                }
                $totdis = $RapTotal == 0 ? 0 : $TotalVal/$Weight;
                $totdisper = $RapTotal == 0 ? 0 : $RapTotal/$totdis;
            @endphp
            <tr>
                <th>{{ count($Products) }}</th>
                <th>{{ ROUND($Weight,2) }}</th>
                <th>{{ $RapTotal == 0 ? 0 : ROUND($RapTotal/$Weight,2) }}</th>
                <th>{{ ROUND($RapTotal,2) }}</th>
                <th>{{ $RapTotal == 0 ? 0 : ROUND((0-100-(0-$totdis/($RapTotal/$Weight))*100),2) }}</th>
                <th>{{ ROUND($totdis,2) }}</th>
                <th>{{ ROUND($TotalVal,2) }}</th>
            </tr>
        </thead>
    </table>
    <br />
    <table class="" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Stone ID</th>
                <th>Lab</th>
                <th>Report No</th>
                <th>Shp</th>
                <th>Carat</th>
                <th>Color</th>
                <th>Clarity</th>
                <th>Cut</th>
                <th>Pol</th>
                <th>Sym</th>
                <th>FL</th>
                <th>Disc%</th>
                <th>$/ct</th>
                <th>Amount</th>
                <th>Measurement</th>
                <th>Ratio</th>
                <th>Image</th>
                <th>Video</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($Products as $Product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $Product->stone_id }}</td>
                    <td>{{ __(App\Models\Product::$Lab[$Product->cert_type]) }}</td>
                    <td><a href="{{ $Product->cert_url }}">{{ $Product->cert_no }}</a></td>
                    <td>{{ $Product->shape_name }}</td>
                    <td>{{ $Product->carat }}</td>
                    <td>{{ $Product->color_name }}</td>
                    <td>{{ $Product->clarity_name }}</td>
                    <td>{{ $Product->cut_name }}</td>
                    <td>{{ $Product->polish_name }}</td>
                    <td>{{ $Product->symmetry_name }}</td>
                    <td>{{ $Product->fluorescence_name }}</td>
                    <td>{{ $Product->discount }}</td>
                    <td>{{ $Product->rate }}</td>
                    <td>{{ $Product->amount }}</td>
                    <td>{{ $Product->measurement }}</td>
                    <td>{{ $Product->ratio }}</td>
                    <td><a href="{{ $Product->image }}">Image</a></td>
                    <td><a href="{{ $Product->video }}">video</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
