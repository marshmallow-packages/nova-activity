<template>
    <div ref="dropdown">
        <div class="tw-inline-flex tw-rounded-md tw-shadow-sm">
            <div class="tw-relative tw--ml-px tw-block">
                <button
                    @click="show_menu = !show_menu"
                    type="button"
                    class=""
                    id="option-menu-button"
                    aria-expanded="true"
                    aria-haspopup="true"
                >
                    <Icon
                        class="tw-h-4 tw-w-4"
                        name="dots-horizontal"
                        :solid="true"
                    />
                </button>
                <div
                    :class="{ 'tw-hidden': !show_menu }"
                    class="tw-absolute tw-right-0 tw-z-10 tw--mr-1 tw-mt-2 tw-w-56 tw-origin-top-right tw-rounded-md tw-bg-white tw-shadow-lg tw-ring-1 tw-ring-black tw-ring-opacity-5 focus:tw-outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="option-menu-button"
                    tabindex="-1"
                >
                    <div class="tw-py-1" role="none">
                        <a
                            v-if="!comment.is_pinned"
                            @click="pinComment()"
                            href="javascript:;"
                            class="tw-text-gray-700 tw-block tw-px-4 tw-py-2 tw-text-sm hover:tw-bg-gray-100 hover:tw-text-gray-900 dark:text-gray-400"
                            role="menuitem"
                            tabindex="-1"
                            id="option-menu-item-0"
                        >
                            {{ __("novaActivity.pin_activity") }}
                        </a>
                        <a
                            v-if="comment.is_pinned"
                            @click="unpinComment()"
                            href="javascript:;"
                            class="tw-text-gray-700 tw-block tw-px-4 tw-py-2 tw-text-sm hover:tw-bg-gray-100 hover:tw-text-gray-900 dark:text-gray-400"
                            role="menuitem"
                            tabindex="-1"
                            id="option-menu-item-0"
                        >
                            {{ __("novaActivity.unpin_activity") }}
                        </a>
                        <a
                            v-if="comment.comment && !comment.is_hidden"
                            @click="hideComment()"
                            href="javascript:;"
                            class="tw-text-gray-700 tw-block tw-px-4 tw-py-2 tw-text-sm hover:tw-bg-gray-100 hover:tw-text-gray-900 dark:text-gray-400"
                            role="menuitem"
                            tabindex="-1"
                            id="option-menu-item-1"
                        >
                            {{ __("novaActivity.hide_comment") }}
                        </a>
                        <a
                            v-if="comment.comment && comment.is_hidden"
                            @click="showComment()"
                            href="javascript:;"
                            class="tw-text-gray-700 tw-block tw-px-4 tw-py-2 tw-text-sm hover:tw-bg-gray-100 hover:tw-text-gray-900 dark:text-gray-400"
                            role="menuitem"
                            tabindex="-1"
                            id="option-menu-item-1"
                        >
                            {{ __("novaActivity.show_comment") }}
                        </a>
                        <a
                            @click="deleteComment()"
                            href="javascript:;"
                            class="tw-text-gray-700 tw-block tw-px-4 tw-py-2 tw-text-sm hover:tw-bg-gray-100 hover:tw-text-gray-900 dark:text-gray-400"
                            role="menuitem"
                            tabindex="-1"
                            id="option-menu-item-2"
                        >
                            {{ __("novaActivity.delete_activity") }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { Icon } from "laravel-nova-ui";

    export default {
        props: ["resourceName", "resourceId", "field", "comment"],

        components: { Icon },

        data() {
            return {
                show_menu: false,
            };
        },

        created() {
            document.addEventListener("click", this.dropdown);
        },
        destroyed() {
            document.removeEventListener("click", this.dropdown);
        },

        methods: {
            pinComment() {
                this.runAction("pin");
            },
            unpinComment() {
                this.runAction("unpin");
            },
            hideComment() {
                this.runAction("hide");
            },
            showComment() {
                this.runAction("show");
            },
            deleteComment() {
                this.runAction("delete");
            },
            runAction(action) {
                this.show_menu = false;
                let self = this;
                let formData = new FormData();
                formData.append("action", action);

                Nova.request()
                    .post(
                        `/nova-vendor/nova-activity/${this.comment.id}/run-action`,
                        formData
                    )
                    .then(
                        (response) => {
                            self.$parent.$parent.loadCommentHistory();
                        },
                        (response) => {
                            Nova.error(response);
                        }
                    )
                    .finally(() => {
                        //
                    });
            },
            dropdown(e) {
                let el = this.$refs.dropdown;
                let target = e.target;
                if (el && el !== target && !el.contains(target)) {
                    this.show_menu = false;
                }
            },
        },
    };
</script>
