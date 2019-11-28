{{ Form::with(null, ['url'=>'/test-other-route']) }}
    @forEach($comments as $comment)
        @fieldsForWith('comment[]', $comment)
            {{ Form::textField('body') }}
        @endFieldsFor
    @endForEach
{{ Form::end() }}
