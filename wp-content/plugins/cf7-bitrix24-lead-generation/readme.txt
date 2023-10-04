=== Contact Form 7 - Bitrix24 CRM - Integration ===
Contributors: https://github.com/drabodan
Tags: bitrix24, bitrix24 leads, business leads, contact form 7, contact form 7 bitrix24, form, integration, lead finder, lead management, lead scraper, leads, marketing leads, sales leads, crm

== Description ==

The main task of this plugin is a send your Contact Form 7 forms directly to your Bitrix24 account.

= Features =

* Integrate your `Contact Form 7` forms with Bitrix24 CRM;
* Works with any edition of Bitrix24 CRM;
* Your can choice that your want to generate - lead, deal, task, contact or company;
* Creation of the deal and the task, occurs together with the creation / binding of the contact and the company. (if their fields are filled);
* Creation of notifications in Bitrix24 CRM when adding a lead, deal and task.
* Fields are loaded from the CRM (including custom fields) (except for tasks);
* You can set up each form personally, specify which information you want to get;
* Sending in two modes: immediately when submitting the form or with a slight delay through `Action Scheduler`;
* Integrate unlimited Contact Form 7 forms;
* Multiple deal pipeline support;
* Supports getting `utm` params from the `URL`;
* Supports for sending `GA Client ID`;
* Supports for sending `roistat_visit` cookie;
* Supports for `_ym_uid` cookie to use;
* Supports for uploaded files for types `lead` and `deal`;
* Compatible with `Contact Form 7 Multi-Step Forms`. (when configuring, you need to fill in the fields with all the steps in the last form);

== Installation ==

1. Extract `cf7-bitrix24-lead-generation.zip` and upload it to your `WordPress` plugin directory
(usually /wp-content/plugins ), or upload the zip file directly from the WordPress plugins page.
Once completed, visit your plugins page.
2. Be sure `Contact Form 7` Plugin is enabled.
3. Activate the plugin through the `Plugins` menu in WordPress.
4. Go to your `Bitrix24` -> `Applications` -> `Web hooks`.
5. Click `ADD WEB HOOK`. Choose `Inbound web hook`.
6. Check `Tasks`, `Tasks (extended permissions)`, `CRM` and `Chat and Notifications (im)`. Click the button `SAVE`.
7. Copy value from `REST call example URL` without `profile/`.
8. Go to the `Contact Form 7` -> `Bitrix24`.
9. Insert in the field `Inbound web hook` copied value.
10. Save settings.
11. When editing forms your can see the tab `Bitrix24`.
