<div
    x-show="$store.modal.open"
    x-transition
    x-cloak
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div
        :class="$store.modal.size"
        @click.outside="$store.modal.close()"
        class="bg-white rounded-xl shadow-lg w-full">

        <!-- Header -->
        <div class="flex justify-between items-center border-b p-5">

            <h2
                class="text-xl font-bold"
                x-text="$store.modal.title">
            </h2>

            <button
                @click="$store.modal.close()"
                class="text-xl">
                ✕
            </button>

        </div>

        <!-- Body -->
        <div
            class="p-6"
            x-html="$store.modal.content">
        </div>

    </div>

</div>