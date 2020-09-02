{{ \Form::with($options['object'], ['url'=>'/test-other-route']) }}
  {{ \Form::radioButton('state', 2, $options) }}
{{ \Form::end() }}