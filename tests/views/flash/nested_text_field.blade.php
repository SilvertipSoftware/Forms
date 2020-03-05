{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::textFieldWithObject('post[some][nested][path]', 'title', $options) }}
{{ \Form::end() }}