@formWith($post, ['url'=>'/test-route'] as $rootForm)
    <div>Test form builder variable: {{ $rootForm->object->title}}</div>
    <div>Test root form scoping: {{ Form::textField('title') }}</div>
    {{ Form::numberField('rating') }}

    @fieldsFor('author')
        <div>Test subform scoping: {{ Form::textField('name') }}</div>
        <div>Test access to other form: {{ $rootForm->checkBox('is_published') }}</div>
    @endFieldsFor

    @forEach($comments as $comment)
        @fieldsFor('comments', $comment, ['child_index'=>$loop->index])
            {{ Form::textField('body') }}
        @endFieldsFor
    @endForEach

    @formButton(['class'=>'flavour flav'])
        Hi there, you!
    @endFormButton
@endForm
