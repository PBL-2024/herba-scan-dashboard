<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends BaseController
{
    /**
     * Add a comment to an article.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function comment(Request $request)
    {
        try {
            $request->validate([
                "komentar" => "string|required",
                "article_id" => "integer|required|exists:articles,id",
            ], [
                "komentar.required" => "Komentar tidak boleh kosong.",
                "komentar.string" => "Komentar harus berupa teks.",
                "article_id.required" => "Article ID tidak boleh kosong.",
                "article_id.integer" => "Article ID harus berupa angka.",
                "article_id.exists" => "Article tidak ditemukan.",
            ]);
        } catch (\Throwable $th) {
            return $this->sendError('Validation Error.', $th->getMessage(), code: 400);
        }

        $comment = new Comment();
        $comment->user_id = $request->user()->id;
        $comment->article_id = $request->article_id;
        $comment->komentar = $request->komentar;
        $comment->save();
        return $this->sendResponse($comment, "Kommentar berhasil ditambahkan.");
    }

    /**
     * Get comments from an article.
     * @param int $article_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getComments($article_id)
    {
        $comments = Comment::with('user')->where('article_id', $article_id)->get();
        if ($comments->isEmpty()) {
            return $this->sendResponse(null, 'Komentar masih kosong.');
        }
        return $this->sendResponse($comments, "Berhasil mengambil data komentar.");
    }

    /**
     * Delete a comment from an article.
     * @param \Illuminate\Http\Request $request
     * @param int $article_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function deleteComment(Request $request, $article_id, $comment_id)
    {
        try {
            $comment = Comment::where("article_id", $article_id)
                ->where("user_id", $request->user()->id)
                ->where('id', $comment_id)->firstOrFail();
            if ($comment) {
                $comment->delete();
                return $this->sendResponse(null, "Komentar berhasil dihapus.");
            }
            return $this->sendError("Komentar tidak ditemukan", null, 404);
        } catch (\Throwable $th) {
            return $this->sendError("Komentar tidak ditemukan", null, 404);
        }
    }
}
