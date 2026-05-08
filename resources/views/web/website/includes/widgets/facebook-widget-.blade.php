{!! Form::open(['route' => 'subscribers.store', 'method' => 'POST', 'class' => 'form-inline',
                                'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
    <input type="text" name="name" class="form-control mb-2 mr-sm-2 col-5" id="inlineFormInputName2" placeholder="Your Name" required>
    <input type="email" name="email" class="form-control mb-2 mr-sm-2 col-5" id="inlineFormInputName2" placeholder="youremail@domain.com" required>
    <button type="submit" class="btn btn-warning" >Subscribe</button>
{!! Form::close() !!}