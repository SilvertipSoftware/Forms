{{ \Form::with($options['object'], ['url'=>'#']) }}
  {{ \Form::collectionSelect('authors', $options['object']->authors, 'id', 'name', $options) }}
{{ \Form::end() }}
