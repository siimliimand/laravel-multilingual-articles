<?php

namespace Tests\Unit;

use App\Enums\NodeType;
use App\Enums\Visibility;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    // =========================================================================
    // NodeType Enum Tests
    // =========================================================================

    /** @test */
    public function node_type_has_article_case(): void
    {
        $this->assertEquals('article', NodeType::ARTICLE->value);
    }

    /** @test */
    public function node_type_has_user_agreement_case(): void
    {
        $this->assertEquals('user_agreement', NodeType::USER_AGREEMENT->value);
    }

    /** @test */
    public function node_type_label_returns_human_readable_name(): void
    {
        $this->assertEquals('Article', NodeType::ARTICLE->label());
        $this->assertEquals('User Agreement', NodeType::USER_AGREEMENT->label());
    }

    /** @test */
    public function node_type_is_article_returns_true_for_article(): void
    {
        $this->assertTrue(NodeType::ARTICLE->isArticle());
    }

    /** @test */
    public function node_type_is_article_returns_false_for_user_agreement(): void
    {
        $this->assertFalse(NodeType::USER_AGREEMENT->isArticle());
    }

    /** @test */
    public function node_type_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(NodeType::tryFrom('invalid_value'));
    }

    /** @test */
    public function node_type_try_from_returns_case_for_valid_value(): void
    {
        $this->assertSame(NodeType::ARTICLE, NodeType::tryFrom('article'));
        $this->assertSame(NodeType::USER_AGREEMENT, NodeType::tryFrom('user_agreement'));
    }

    /** @test */
    public function node_type_serializes_to_string_value(): void
    {
        $this->assertEquals('article', (string) NodeType::ARTICLE->value);
        $this->assertEquals('user_agreement', (string) NodeType::USER_AGREEMENT->value);
    }

    // =========================================================================
    // Visibility Enum Tests
    // =========================================================================

    /** @test */
    public function visibility_has_public_case(): void
    {
        $this->assertEquals('public', Visibility::PUBLIC->value);
    }

    /** @test */
    public function visibility_has_private_case(): void
    {
        $this->assertEquals('private', Visibility::PRIVATE->value);
    }

    /** @test */
    public function visibility_label_returns_human_readable_name(): void
    {
        $this->assertEquals('Public', Visibility::PUBLIC->label());
        $this->assertEquals('Private', Visibility::PRIVATE->label());
    }

    /** @test */
    public function visibility_is_public_returns_true_for_public(): void
    {
        $this->assertTrue(Visibility::PUBLIC->isPublic());
    }

    /** @test */
    public function visibility_is_public_returns_false_for_private(): void
    {
        $this->assertFalse(Visibility::PRIVATE->isPublic());
    }

    /** @test */
    public function visibility_try_from_returns_null_for_invalid_value(): void
    {
        $this->assertNull(Visibility::tryFrom('invalid_value'));
    }

    /** @test */
    public function visibility_try_from_returns_case_for_valid_value(): void
    {
        $this->assertSame(Visibility::PUBLIC, Visibility::tryFrom('public'));
        $this->assertSame(Visibility::PRIVATE, Visibility::tryFrom('private'));
    }

    /** @test */
    public function visibility_serializes_to_string_value(): void
    {
        $this->assertEquals('public', (string) Visibility::PUBLIC->value);
        $this->assertEquals('private', (string) Visibility::PRIVATE->value);
    }

    // =========================================================================
    // Enum JSON Serialization Tests
    // =========================================================================

    /** @test */
    public function node_type_json_serialization(): void
    {
        $data = ['type' => NodeType::ARTICLE];
        $json = json_encode($data);
        $decoded = json_decode($json, true);

        $this->assertEquals('article', $decoded['type']);
    }

    /** @test */
    public function visibility_json_serialization(): void
    {
        $data = ['visibility' => Visibility::PUBLIC];
        $json = json_encode($data);
        $decoded = json_decode($json, true);

        $this->assertEquals('public', $decoded['visibility']);
    }
}
