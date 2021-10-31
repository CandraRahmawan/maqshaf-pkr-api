<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $value)
        <tr>
            <td>{{ $value->totalSantriActive }}</td>
            <td>{{ $value->totalDeposit }}</td>
        </tr>
    @endforeach
    </tbody>
</table>