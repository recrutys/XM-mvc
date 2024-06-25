<?php

namespace XM\Install;

class Phrases
{
	public function getPhrases(): array
	{
		return [
			1 => [
				'do_not_have_permission'         => 'You do not have permission to view this page or perform this action.',
				'requested_page_not_found'       => 'The requested page could not be found.',
				'action_available_via_post_only' => 'This action is available via POST only. Please press the back button and try again.'
			]
		];
	}
}