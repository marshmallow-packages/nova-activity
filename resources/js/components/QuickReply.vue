<template>
    <div>
        <div class="tw-relative">
            <button
                type="button"
                class="tw-relative tw--m-2.5 tw-flex tw-h-10 tw-w-10 tw-items-center tw-justify-center tw-rounded-full tw-text-gray-400 hover:tw-text-gray-500"
                aria-haspopup="listbox"
                aria-expanded="true"
                aria-labelledby="listbox-label"
            >
                <span
                    @click="openQuickReply()"
                    class="tw-flex tw-items-center tw-justify-center"
                >
                    <!-- Placeholder label, show/hide based on listbox state. -->
                    <span v-if="!quick_reply">
                        <svg
                            class="tw-h-5 tw-w-5 tw-flex-shrink-0"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.536-4.464a.75.75 0 10-1.061-1.061 3.5 3.5 0 01-4.95 0 .75.75 0 00-1.06 1.06 5 5 0 007.07 0zM9 8.5c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S7.448 7 8 7s1 .672 1 1.5zm3 1.5c.552 0 1-.672 1-1.5S12.552 7 12 7s-1 .672-1 1.5.448 1.5 1 1.5z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </span>
                    <span v-if="quick_reply">
                        <span
                            :style="{
                                background:
                                    field.quick_replies[quick_reply].background,
                                color:
                                    field.quick_replies[quick_reply].color ??
                                    '#fff',
                            }"
                            :class="{
                                'tw-w-8 tw-h-8': icon_size_class == 'normal',
                                'tw-w-6 tw-h-6': icon_size_class == 'small',
                            }"
                            class="tw-flex tw-items-center tw-justify-center tw-rounded-full"
                        >
                            <Icon
                                :width="icon_size_class == 'normal' ? 24 : 16"
                                :type="field.quick_replies[quick_reply].icon"
                                :solid="
                                    field.quick_replies[quick_reply].solid_icon
                                "
                            />
                        </span>
                    </span>
                </span>
            </button>
            <ul
                v-if="show_quick_reply_selector"
                class="tw-absolute tw-z-10 tw--ml-6 tw-mt-1 tw-w-60 tw-rounded-lg dark:tw-bg-none tw-bg-white tw-py-3 tw-text-base tw-shadow tw-ring-1 tw-bottom-0 tw-ring-black tw-ring-opacity-5 focus:tw-outline-none sm:tw-ml-auto sm:tw-w-64 sm:tw-text-sm"
                tabindex="-1"
                role="listbox"
                aria-labelledby="listbox-label"
                aria-activedescendant="listbox-option-5"
            >
                <li
                    @click="handleQuickReply(quick_reply_value)"
                    v-for="(
                        quick_reply, quick_reply_value
                    ) in field.quick_replies"
                    v-bind:key="quick_reply_value"
                    class="tw-bg-white dark:tw-bg-none tw-cursor-pointer hover:tw-bg-gray-100 tw-relative tw-select-none tw-px-3 tw-py-2"
                    id="listbox-option-0"
                    role="option"
                >
                    <div class="tw-flex tw-items-center">
                        <div
                            :style="{
                                background: quick_reply.background,
                                color: quick_reply.color ?? '#fff',
                            }"
                            class="tw-flex tw-h-6 tw-w-6 tw-items-center tw-justify-center tw-rounded-full"
                        >
                            <Icon
                                width="16"
                                :type="quick_reply.icon"
                                :solid="quick_reply.solid_icon"
                            />
                        </div>
                        <span
                            class="tw-ml-3 tw-block tw-truncate tw-font-medium"
                        >
                            {{ quick_reply.name }}
                        </span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default {
        props: ["action", "field", "current_value", "size", "comment_id"],

        data: () => ({
            quick_reply: "",
            icon_size_class: "normal",
            show_quick_reply_selector: false,
        }),

        created() {
            if (this.size) {
                this.icon_size_class = this.size;
            }
            this.quick_reply = this.current_value;
        },

        methods: {
            reset() {
                this.quick_reply = '';
            },
            handleQuickReply(quick_reply) {
                if (this.action == "new_comment") {
                    this.quick_reply = quick_reply;
                    this.$parent.setQuickReply(quick_reply);
                    this.show_quick_reply_selector = false;
                } else {
                    this.quick_reply = quick_reply;
                    this.show_quick_reply_selector = false;
                    this.updateQuickReply();
                }
            },
            openQuickReply() {
                this.show_quick_reply_selector = true;
            },

            updateQuickReply() {
                let formData = new FormData();
                formData.append("quick_reply", this.quick_reply);
                return Nova.request()
                    .post(
                        `/nova-vendor/nova-activity/${this.comment_id}/set-quick-reply`,
                        formData
                    )
                    .then(
                        (response) => {
                            if (response.data.success) {
                                Nova.success(response.data.message);
                                this.loadCommentHistory();
                            } else {
                                Nova.error(response.data.message);
                            }
                        },
                        (response) => {
                            Nova.error(response);
                        }
                    )
                    .finally(() => {
                        setTimeout(() => {
                            self.action_run_successfully = false;
                        }, 2500);
                    });
            },
        },
    };
</script>
