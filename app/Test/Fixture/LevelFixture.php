<?php
class LevelFixture extends CakeTestFixture {
    public $import = array('model' => 'Level', 'records' => false);
    public $records = array(
            array(
                    'name' => 'level',
                    'content' => "LevelName level\nScript test.levelgen",
                    'level_filename' => 'alice_level.level',
                    'levelgen' => 'some levelgen code',
                    'levelgen_filename' => 'test.levelgen',
                    'description' => 'descriptive',
                    'user_id' => 1,
                    'downloads' => 1,
                    'last_updated' => 0,
                    'id' => 1
            ),
            array(
                    'name' => 'level',
                    'content' => 'empty (more or less)',
                    'level_filename' => 'bob_level.level',
                    'levelgen' => '',
                    'levelgen_filename' => 'test.levelgen',
                    'description' => 'descriptive',
                    'user_id' => 2,
                    'downloads' => 1,
                    'last_updated' => 0,
                    'id' => 2
            ),
            array(
                    'name' => "bob's level",
                    'content' => 'content',
                    'level_filename' => 'bob_bobs_level.level',
                    'levelgen' => '',
                    'levelgen_filename' => 'test.levelgen',
                    'description' => 'uploaded by bob',
                    'user_id' => 2,
                    'downloads' => 2,
                    'last_updated' => 0,
                    'id' => 3
            ),
            array(
                    'name' => "another level by bob",
                    'content' => 'content',
                    'level_filename' => 'bob_another_level_by_bob.level',
                    'levelgen' => '',
                    'levelgen_filename' => '',
                    'description' => 'uploaded by bob',
                    'user_id' => 2,
                    'downloads' => 2,
                    'last_updated' => 0,
                    'id' => 4
            ),
    );
}
