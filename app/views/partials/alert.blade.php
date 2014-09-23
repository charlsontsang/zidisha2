<script>
    $.growl('{{{ $message }}}', { 
        type: '{{ $level  }}',
        allow_dismiss: true,
        delay: 0,
        z_index: 2000
    });
</script>
