{{ \Form::with($options['object'], ['url'=>'/test-other-route']) }}
  {{ \Form::textArea('body', $options) }}
{{ \Form::end() }}