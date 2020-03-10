{{ \Form::with($options['object'], ['url'=>'#']) }}
  {{ \Form::textArea('body') }}
  @foreach ($options['object']['comments'] as $comment)
    {{ \Form::fieldsFor('comments', $comment, ['index' => $loop->index]) }}
        {{ \Form::label('id') }}
        {{ \Form::textArea('body') }}
    {{ \Form::end() }}
  @endforeach
{{ \Form::end() }}