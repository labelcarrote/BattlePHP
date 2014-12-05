{************************************************

 Button Delete Event

 in :
 - $battle

************************************************}
<form method="POST">
	<input type="hidden" name="event_id" value="{$event->id}">
	<button class="btn btn-default btn-link" name="submit" value="delete_event" title="Delete Event">delete<!--  <i class="fa fa-trash"></i> --></button>
</form>
