<?php



use App\ArticleModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_article_can_be_added(){
        $this->withoutExceptionHandling( );

        $this->post( url('/public/api/articles' ), [ 'title' => 'test name', 'body' => ' this is test body' ] );

        $this->assertCount(1, ArticleModel::all( ) );
    }
}
