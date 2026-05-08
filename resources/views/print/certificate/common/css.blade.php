<link href="https://fonts.googleapis.com/css?family=Roboto:wght@400;500;700|Holtwood+One+SC|Fugaz+One|Lobster|Merienda|Righteous|Black+Ops+One|Gilda+Display" rel="stylesheet">

<style>
    {{--@font-face {--}}
    {{--    font-family: certificateFont;--}}
    {{--    src: url({{ asset('fonts/Call421.ttf') }});--}}
    {{--}--}}
 .main-content{
        font-family: Roboto !important;
        font-size:13px ;
    }
    .institute-detail{
        height: 110px;
    }
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        /*font: 12pt "Tahoma";*/

    }
    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }


    .page {
        width: 210mm;
        height: 297mm;
        margin: 0 auto;
        /*border: 1px #D3D3D3 solid;*/
        border-radius: 5px;
        background: white;
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.1);
        /*font-family: certificateFont !important;*/
        font-size:13px ;
    }

    .page-content{
        background-color: transparent !important;
    }
    .subpage {
        width: 210mm;
        height: 297mm;
        margin: 0px auto;
        padding: 10px;
        /*height: 257mm;*/
    }

    .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
        padding: 4px !important;
    }

    @page {
        size: A4;
        margin: 0;
    }
    @media print {
        html, body {
            width: 210mm;
            height: 297mm;
        }
        .page {
            margin: 0 auto;
            /*border: initial;*/
            /*border-radius: initial;*/
            /*width: initial;*/
            /*min-height: initial;*/
            /*box-shadow: initial;*/
            /*background: initial;*/
        }

    }
    .col-sm-12.align-right.hidden-print {
        top:25%;
        position: fixed;
        z-index: 999;
    }
</style>