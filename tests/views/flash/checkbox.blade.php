{{ \Form::with($options['object'], ['url'=>'/test-other-route']) }}
  {{ \Form::checkBox('is_published', $options) }}
{{ \Form::end() }}