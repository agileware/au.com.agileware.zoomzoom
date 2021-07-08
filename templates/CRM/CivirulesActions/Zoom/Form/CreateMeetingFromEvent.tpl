<h3>{$ruleActionHeader}</h3>
<div id="help">{ts}Configure settings for the Zoom Meeting. For more details please see https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate{/ts}</div>
<div class="crm-block crm-form-block">
    <div class="crm-section">
        <div class="label">{$form.duration.label}</div>
        <div class="content">{$form.duration.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.schedule_for.label}</div>
        <div class="content">{$form.schedule_for.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.password.label}</div>
        <div class="content">{$form.password.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.host_video.label}</div>
        <div class="content">{$form.host_video.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.participant_video.label}</div>
        <div class="content">{$form.participant_video.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.join_before_host.label}</div>
        <div class="content">{$form.join_before_host.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.jbh_time.label}</div>
        <div class="content">{$form.jbh_time.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.mute_upon_entry.label}</div>
        <div class="content">{$form.mute_upon_entry.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.watermark.label}</div>
        <div class="content">{$form.watermark.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.approval_type.label}</div>
        <div class="content">{$form.approval_type.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.audio.label}</div>
        <div class="content">{$form.audio.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.auto_recording.label}</div>
        <div class="content">{$form.auto_recording.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.alternative_hosts.label}</div>
        <div class="content">{$form.alternative_hosts.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.close_registration.label}</div>
        <div class="content">{$form.close_registration.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.waiting_room.label}</div>
        <div class="content">{$form.waiting_room.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.global_dial_in_countries.label}</div>
        <div class="content">{$form.global_dial_in_countries.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.contact_name.label}</div>
        <div class="content">{$form.contact_name.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.contact_email.label}</div>
        <div class="content">{$form.contact_email.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.registrants_email_notification.label}</div>
        <div class="content">{$form.registrants_email_notification.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.meeting_authentication.label}</div>
        <div class="content">{$form.meeting_authentication.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.authentication_domains.label}</div>
        <div class="content">{$form.authentication_domains.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.show_share_button.label}</div>
        <div class="content">{$form.show_share_button.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.allow_multiple_devices.label}</div>
        <div class="content">{$form.allow_multiple_devices.html}</div>
        <div class="clear"></div>
    </div>
    <div class="crm-section">
        <div class="label">{$form.alternative_hosts_email_notification.label}</div>
        <div class="content">{$form.alternative_hosts_email_notification.html}</div>
        <div class="clear"></div>
    </div>
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
