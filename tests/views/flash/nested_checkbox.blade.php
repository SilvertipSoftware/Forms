{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::checkBoxWithObject('post[some][nested][path]', 'is_published', $options) }}
{{ \Form::end() }}