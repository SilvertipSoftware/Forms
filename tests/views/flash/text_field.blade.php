{{ \Form::with($options['object'], ['url'=>'/test-other-route']) }}
  {{ \Form::textField('title', $options) }}
{{ \Form::end() }}