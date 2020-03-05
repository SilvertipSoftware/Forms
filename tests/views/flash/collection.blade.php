{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::collectionSelectWithObject('post', 'author_id', $authors, 'id', 'name', $options) }}
{{ \Form::end() }}