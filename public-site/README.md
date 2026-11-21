# Hexycle 公開候補 — ローカル確認手順

Cloudflare Pagesの静的配信ディレクトリとして、このフォルダーの内容を使用します。各ページは `index.html` を持ち、URLはルート基準です。Cloudflare Pages以外で確認する場合、Python 3があればこのフォルダーで `python -m http.server 8000` を実行し、`http://127.0.0.1:8000/` にアクセスしてください。HTMLを直接ダブルクリックした場合、ルート基準のリンクと3Dが動かないことがあります。

Pages Preview・本番デプロイ、mainへのマージはまだ行っていません。画像・文章の最終確認とPreview試験の後に公開可否を判断してください。

3D ExplorerはPhase 4比較モデルの同梱版を使用。`explorer/index.html` にHome・サイト内ナビを追加し、`model.js`、`app.js`、`style.css`、CAD画像、Three.jsは変更していません。
