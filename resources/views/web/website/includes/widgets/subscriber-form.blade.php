<style>
    .card-blue{
        padding: 10px;
        background: red;
        border-radius: 5px;
    }
    form input{
        margin-bottom: 10px;
    }

    h3.subscribe-widget-heading{
        font-weight: 600;
        margin: 0 auto !important;

    }
    .subscribe-widget-button{
        width: 100% !important;
        margin-top: 5px;
    }
</style>

<aside class="sidebar right-sidebar">
    <!--Category Blog-->
    <div class="sidebar-widget categories-blog">
        <div class="inner-box">
            {!! Form::open(['route' => 'web.subscribers.store', 'method' => 'POST', 'class' => 'form-inline',
                               'id' => 'validation-form', "enctype" => "multipart/form-data"]) !!}
            <h3 class="subscribe-widget-heading">Subscribe Newsletter</h3>
            <input type="text" name="name" class="form-control mb-12 mr-sm-12 col-12" id="inlineFormInputName2" placeholder="Your Name" required>
            <input type="email" name="email" class="form-control mb-12 mr-sm-12 col-12" id="inlineFormInputName2" placeholder="youremail@domain.com" required>
            <br>
            <button type="submit" class="btn btn-warning subscribe-widget-button" style="cursor: pointer;width: auto;">Subscribe</button>
            {!! Form::close() !!}
        </div>
    </div>

</aside>
