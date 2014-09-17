<table class="table table-striped">
    <thead>
    <tr>
        <th>File</th>
        <th><span id="untranslated" data-toggle="tooltip" data-placement="top" title="Untranslated fields in the file">Untranslated</span></th>
        <th><span id="updated" data-toggle="tooltip" data-placement="top" title="Updated fields in the file">Updated</span></th>
    </tr>
    </thead>
    <tbody>
    @foreach($files as $file)
    @if($file['totalUntranslated'] > 0)
    <tr class="danger">
        @elseif($file['totalUpdated'] > 0)
    <tr class="warning">
        @else
    <tr>
        @endif
        <td>
            <a href="{{ route('admin:translation', [$folder, $file['filename'], 'languageCode' => $languageCode]) }}">
                {{ $file['filename'] }}
            </a>
        </td>
        <td>
            {{ $file['totalUntranslated'] }}
        </td>
        <td>
            {{ $file['totalUpdated'] }}
        </td>
    </tr>
    @endforeach
    </tbody>
</table>