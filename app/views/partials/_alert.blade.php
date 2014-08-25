<script>
    $.growl('{{{ $message }}}', { 
        type: '{{ $level  }}',
        allow_dismiss: true,
        delay: 0
    });
</script>
