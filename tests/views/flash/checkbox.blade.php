{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::checkBoxWithObject('post', 'is_published', $options) }}
{{ \Form::end() }}