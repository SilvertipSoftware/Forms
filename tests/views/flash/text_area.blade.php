{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::textAreaWithObject('post', 'body', $options) }}
{{ \Form::end() }}