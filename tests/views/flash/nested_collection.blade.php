{{ \Form::with(null, ['url'=>'/test-other-route']) }}
  {{ \Form::collectionSelectWithObject('post[some][nested][path]', 'author_id', $authors, 'id', 'name', $options) }}
{{ \Form::end() }}