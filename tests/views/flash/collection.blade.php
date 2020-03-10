{{ \Form::with($options['object'], ['url'=>'/test-other-route']) }}
  {{ \Form::collectionSelect('author_id', $authors, 'id', 'name', $options) }}
{{ \Form::end() }}