Hi

@formWith($post, ['url'=>'/test-route'] as $rootForm)
    <div>Test form builder variable: {{ $rootForm->object->title}}</div>
    <div>Test root form scoping: {{ Form::textField('title') }}</div>
    {{ Form::numberField('rating') }}

    @fieldsFor('author')
        <div>Test subform scoping: {{ Form::textField('name') }}</div>
        <div>Test access to other form: {{ $rootForm->checkBox('is_published') }}</div>
    @endFieldsFor
@endForm

{{ Form::with(null, ['url'=>'/test-other-route']) }}
    @forEach($comments as $comment)
        @fieldsForWith('comment[]', $comment)
            {{ Form::textField('body') }}
        @endFieldsFor
    @endForEach
{{ Form::end() }}