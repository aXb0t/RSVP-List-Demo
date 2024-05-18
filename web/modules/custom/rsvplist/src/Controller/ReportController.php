<?php

/**
 * @file
 * Provide site administrators with a list of all the RSVP list signups
 * so they know who is attending their events.
 */
namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;

/**
 * Controller for RSVP List reports.
 */
class ReportController extends ControllerBase
{
    /**
     * Returns a list of all RSVP'd users for an event.
     *
     * @param int $event_id
     * The ID of the event.
     *
     * @return array
     * A render array for the report page.
     */

    protected function load()
    {
        try {
            $database = \Drupal::database();
            $select_query = $database->select("rsvplist", "r");

            // join the user table, so we can get the entry creators username
            $select_query->join("users_field_data", "u", "r.uid = u.uid");
            // join the node table so we can get the event name
            $select_query->join("node_field_data", "n", "r.nid = n.nid");

            $select_query->addField("u", "name", "username");
            $select_query->addField("n", "title");
            $select_query->addField("r", "mail");

            $entries = $select_query->execute()->fetchAll(\PDO::FETCH_ASSOC);

            return $entries;

        } catch (\Exception $e) {
            watchdog_exception("rsvplist", $e->getMessage());

            drupal_set_message(
                t('An error occurred loading the event. Please try again
later.'),
                "error"
            );

            return NULL;
        }
    }
    /**
     * Creates the RSVPList report page.
     *
     * @return array
     * A Render array for the RSVPList report outlet.
     */
    public function report()
    {
        // Code to create RSVPList report page.
        $content = [];

        $content["message"] = [
            "#markup" => t('Below is a list of all event RSVPs including username, email
address and the name of the event they will be attending.'),
        ];

        $headers = [t("Username"), t("Event"), t("Email")];

        // because load() returns an associative array with each talbe row
        // as its own array, we can simply devine the HTML table rows:
        $table_rows = $this->load();

        // however, as an example, if load() did not return the results in
        // a structure compatible with what we need, we could populate the
        // $table_rows variable like so:

        // $table_rows = [];
        // // load the entries from the database.
        // $rsvp_entries = $this->load();
        // // Go through each entry and add it to $rows.
        // // Ultimately each array will be rendered as a row in an HTML table.
        // foreach ($rsvp_entries as $entry) {
        // $table_rows[] = $entry;
        // }

        // Build the table element using $headers and $table_rows.
        $content["table"] = [
            "#type" => "table",
            "#header" => $headers,
            "#rows" => $table_rows,
            "#empty" => t("No entries available"),
        ];

        // no cache
        $content["#cache"]["max-age"] = 0;

        return $content;
    }
}
