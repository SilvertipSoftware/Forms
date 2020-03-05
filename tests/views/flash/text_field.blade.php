{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::textFieldWithObject('post', 'title', $options) }}
{{ \Form::end() }}