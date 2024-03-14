
        <table class="table-listing table table-bordered table-striped table-sm">
            <thead class="thead-light">
                <tr>
                    <th colspan="4">(A) Asset</th>
                    </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                @foreach ($asset as $key => $group)
                 <?php $total += $group['total_amount']; ?>
                    <tr>
                        <td colspan="3"> {{ $group['group_name'] }} </td>
                        <td>{{ $group['total_amount'] }}</td>
                    </tr>
                    @foreach ($group['items'] as $value)
                    <tr>
                        <td> </td>
                        <td>{{ $value['name'] }}</td>
                        <td>{{ $value['amount'] }}</td>
                        <td></td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3"> Total </td>
                    <td>{{ $total }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table-listing table table-bordered table-striped table-sm" style="margin-left: 20px;">
            <thead class="thead-light">
                <tr>
                    <th colspan="4">(B) Liability</th>
                    </tr>
            </thead>
            <tbody>
                <?php $totaliability = 0; ?>
                @foreach ($liability as $key => $group)
                <?php $totaliability += $group['total_amount']; ?>
                    <tr>
                        <td colspan="3"> {{ $group['group_name'] }} </td>
                        <td>{{ $group['total_amount'] }}</td>
                    </tr>
                    @foreach ($group['items'] as $value)
                    <tr>
                        <td> </td>
                        <td>{{ $value['name'] }}</td>
                        <td>{{ $value['amount'] }}</td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td colspan="3"> Total </td>
                    <td>{{ $totaliability }}</td>
                </tr>
            </tbody>
        </table>
    
