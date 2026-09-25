@extends('layouts.public')

@section('content')
    <section class="px-6 py-16">
        <div class="max-w-5xl mx-auto">
            <p class="text-sm font-semibold text-green-700 mb-2">
                Vertical Farming Project
            </p>

            <h1 class="text-4xl font-bold mb-6">
                Hexycle
            </h1>

            <p class="text-xl leading-relaxed mb-8">
                Hexycleは、限られた空間で効率よく植物を育てることを目指した
                垂直農法プロジェクトです。
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('products.index') }}"
                   class="px-5 py-3 bg-green-700 text-white rounded-lg hover:bg-green-800">
                    苗を見る
                </a>

                <a href="{{ route('events.show', 1) }}"
                   class="px-5 py-3 border border-gray-400 rounded-lg hover:bg-gray-100">
                    文化祭について
                </a>
            </div>
        </div>
    </section>

    <section class="px-6 py-12 bg-white">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">
                Hexycleとは
            </h2>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="p-5 border rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">
                        垂直農法
                    </h3>
                    <p>
                        複数段の育成空間を利用し、
                        小さな設置面積でも複数の植物を育成します。
                    </p>
                </div>

                <div class="p-5 border rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">
                        水耕栽培
                    </h3>
                    <p>
                        NFT方式を用いて養液を循環させ、
                        植物の根へ水分と養分を供給します。
                    </p>
                </div>

                <div class="p-5 border rounded-lg">
                    <h3 class="font-semibold text-lg mb-2">
                        データ管理
                    </h3>
                    <p>
                        センサーデータを記録し、
                        育成状態をWeb上から確認できる仕組みを目指しています。
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="px-6 py-12">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-2xl font-bold mb-4">
                文化祭での取り組み
            </h2>

            <p class="mb-6">
                文化祭では、Hexycleで育成した苗の紹介・予約・販売を行い、
                購入者が育成状態を確認できるWebシステムも運用する予定です。
            </p>

            <a href="{{ route('events.show', 1) }}"
               class="text-green-700 font-semibold hover:underline">
                文化祭イベントを見る →
            </a>
        </div>
    </section>
@endsection