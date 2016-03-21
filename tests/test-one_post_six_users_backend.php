<?php
//testcase :check whether only admin, editor and author is allowed to edit post and change contributors meta options
class Tests_One_Post_Six_Users extends WP_UnitTestCase {
	protected $author = 0;
        protected $author_id = 0;
        protected $subscriber = 0;
protected $admin = 0;
protected $editor = 0;
protected $contributor = 0;
	protected $post_id = 0;

	private $permalink_structure;

	function setUp() {
		parent::setUp();

	$this->author =  self::factory()->user->create( array( 'role' => 'author' ) ) ;
       $this->author_id =  self::factory()->user->create( array( 'role' => 'author' ) ) ;
		$this->admin =  self::factory()->user->create( array( 'role' => 'administrator' ) ) ;
		$this->subscriber =  self::factory()->user->create( array( 'role' => 'subscriber' ) ) ;
		$this->editor =  self::factory()->user->create( array( 'role' => 'editor' ) ) ;
		$this->contributor = self::factory()->user->create( array( 'role' => 'contributor' ) ) ;
         $post = array(
			'post_author' => $this->author_id,
			'post_status' => 'publish',
			'post_content' => 'Test Content',
			'post_title' => 'Test Title',
			'post_type' => 'post'
		);

		// insert a post and make sure the ID is ok
		$this->post_id = self::factory()->post->create( $post );

	}
function test_check_current_user_capability() {
$pinstance = new Wpi_Post_Init();
wp_set_current_user( $this->subscriber);
$this->assertEquals(0, $pinstance->wpi_check_current_user_cap($this->post_id));
wp_set_current_user( $this->contributor);
$this->assertEquals(0, $pinstance->wpi_check_current_user_cap($this->post_id));
$this->assertTrue( current_user_can_for_blog( get_current_blog_id(), 'edit_posts' ) );
wp_set_current_user( $this->admin);
$this->assertEquals(1, $pinstance->wpi_check_current_user_cap($this->post_id));
wp_set_current_user( $this->editor);
$this->assertEquals(1, $pinstance->wpi_check_current_user_cap($this->post_id));
wp_set_current_user( $this->author);
$this->assertEquals(0, $pinstance->wpi_check_current_user_cap($this->post_id));
wp_set_current_user( $this->author_id);
$this->assertEquals(1, $pinstance->wpi_check_current_user_cap($this->post_id));

}

}
