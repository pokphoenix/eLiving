@extends('main.layouts.main')
@section('style')
<link href="{{ url('plugins/fullcalendar/fullcalendar.min.css') }}" rel='stylesheet' />
<link href="{{ url('plugins/fullcalendar/fullcalendar.print.min.css') }}" rel='stylesheet' media='print' />
<style>

  /*body {
    margin: 40px 10px;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }*/

 /* #routine-calendar {
    max-width: 900px;
    margin: 0 auto;
  }*/

</style>
@endsection
@section('content-wrapper')
<!-- data-toggle="modal" data-target="#modal-card-content" -->
  

  
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <img class="icon-title" src="{{ asset('public/img/icon/icon_routine_view_2.png') }}"> Routine Schedule
        <small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Task</li>
      </ol>
    </section>
   
  
    <!-- Main content -->
    <section class="content">
      
     <div id='routine-calendar'></div>

    </section>
    <!-- /.content -->

  <!-- /.content-wrapper -->

@endsection

@section('javascript')


<script src="{{ url('plugins/fullcalendar/fullcalendar.min.js') }}"></script>
<script src="{{ url('plugins/fullcalendar/locale-all.js') }}"></script>
<script>

  $(document).ready(function() {
    var initialLocaleCode = 'th';
    $('#routine-calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay,listWeek'
      },
     showNonCurrentDates:false,

      locale: initialLocaleCode,
      defaultDate: '2017-12-12',
      navLinks: true, // can click day/week names to navigate views
      editable: true,
       businessHours: true, // display business hours
      eventLimit: true, // allow "more" link when too many events
     
      events: [
        {
          title: 'Meeting',
          start: '2017-12-13T11:00:00',
          constraint: 'availableForMeeting', // defined below
          color: '#257e4a',
          icon : "check" 
        },
        {
          title: 'All Day Event',
          start: '2017-12-01'
        },
        {
          title: 'Long Event',
          start: '2017-12-07',
          end: '2017-12-10'
        },
        {
          id: 999,
          title: 'Repeating Event',
          start: '2017-12-09T16:00:00',
          color: '#257e4a'
        },
        {
          id: 999,
          title: 'Repeating Event',
          start: '2017-12-16T16:00:00'
          ,color: '#257e4a'
        },
        {
          title: 'Conference',
          start: '2017-12-11',
          end: '2017-12-13'
        },
        {
          title: 'Meeting',
          start: '2017-12-12T10:30:00',
          end: '2017-12-12T12:30:00'
        },
        {
          title: 'Lunch',
          start: '2017-12-12T12:00:00',
          color:'#00a65a',
          category: 'Q & A',
          category_color:'#00c0ef',
          is_check : 1 ,
          routine_id : 1
        },
        {
          title: '55 Meeting',
          start: '2017-12-12T14:30:00',
          is_check : 0 ,
        },
        {
          title: 'Happy Hour',
          start: '2017-12-12T17:30:00'
        },
        {
          title: 'Dinner',
          start: '2017-12-12T20:00:00'
        },
        {
          title: 'Birthday Party',
          start: '2017-12-13T07:00:00'
        },
        {
          title: 'Click for Google',
          url: 'http://google.com/',
          start: '2017-12-28'
        },
         // areas where "Meeting" must be dropped
        {
          id: 'availableForMeeting',
          start: '2017-12-11T10:00:00',
          end: '2017-12-11T16:00:00',
          rendering: 'background'
        },
        {
          id: 'availableForMeeting',
          start: '2017-12-13T10:00:00',
          end: '2017-12-13T16:00:00',
          rendering: 'background'
        },
         // red areas where no events can be dropped
        {
          start: '2017-12-24',
          end: '2017-12-28',
          overlap: false,
          rendering: 'background',
          color: '#ff9f89'
        },
        {
          start: '2017-12-06',
          end: '2017-12-08',
          overlap: false,
          rendering: 'background',
          color: '#ff9f89'
        }
      ],
        eventRender: function(event, element) {
          console.log('event.is_check',event.is_check);
         if(event.is_check){          
            element.find(".fc-title").append(" <span class='pull-right'><i class='fa fa-check'></i></span>");
            element.find(".fc-event-dot").append("<i class=\"fa fa-check\" style=\"color:#FFF;font-size:10px;position:absolute;\"></i>");
         }
         if(event.category){          
            element.find(".fc-title").append(" <span class='label ' style='background:"+event.category_color+"' >"+event.category+"</span>");
            element.find(".fc-list-item-title").append(" <span class='label ' style='background:"+event.category_color+"' >"+event.category+"</span>");
         }
    },    
    eventClick:function(event, element){
                 //do the ajax call
        if(event.is_check){
          event.is_check = 0;
        }else{
          event.is_check = 1;
        }

        $('#routine-calendar').fullCalendar('updateEvent',event);
        }
    });

  });

</script>

@endsection   
