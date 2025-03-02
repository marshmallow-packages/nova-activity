<template>
    <li v-if="!hidden" class="tw-relative tw-flex tw-gap-x-4">
        <div
            class="tw-absolute tw-left-0 tw-top-0 tw-flex tw-w-6 tw-justify-center tw--bottom-6"
        >
            <div class="tw-w-px tw-bg-gray-200 dark:tw-bg-gray-700"></div>
        </div>

        <img
            v-if="comment.user.avatar"
            :src="comment.user.avatar"
            alt=""
            class="tw-relative tw-mt-3 tw-h-6 tw-w-6 tw-flex-none tw-rounded-full tw-bg-gray-50"
        />
        <div
            v-if="!comment.user.avatar && comment.user.icon"
            class="tw-relative tw-text-center tw-mt-3 tw-h-6 tw-w-6 tw-flex-none tw-rounded-full tw-bg-gray-50"
        >
            <Icon
                class="tw-h-4 tw-w-4"
                :name="comment.user.icon"
                :solid="true"
            />
        </div>
        <div
            class="tw-flex-auto tw-mt-3 tw-rounded-md tw-p-3 tw-ring-1 tw-ring-inset tw-ring-gray-200 dark:tw-ring-gray-700 dark:tw-bg-gray-700"
        >
            <div class="tw-flex tw-justify-between tw-gap-x-4">
                <div
                    class="tw-py-0.5 tw-cursor-pointer tw-text-xs tw-leading-5 tw-text-gray-500"
                    @click="toggleComment()"
                >
                    <div
                        class="tw-font-medium dark:text-gray-400 tw-text-gray-900 tw-flex"
                    >
                        <ActivityStateIcons :comment="comment" />
                        <div class="dark:text-gray-200">
                            {{ comment.user.name }}
                            <span
                                class="tw-text-gray-500 dark:tw-text-gray-400"
                                >{{ comment.type.label }}</span
                            >
                        </div>
                    </div>
                </div>
                <div class="tw-flex tw-gap-x-2">
                    <time
                        class="tw-flex-none tw-py-0.5 tw-text-xs tw-leading-5 dark:text-gray-400 tw-text-gray-500"
                    >
                        <span v-if="field.use_human_readable_dates">
                            {{ comment.time_ago }}
                        </span>
                        <span v-if="!field.use_human_readable_dates">
                            {{ comment.created_at }}
                        </span>
                    </time>
                    <ActiviyActions :comment="comment" />
                </div>
            </div>
            <div
                v-if="show_comment"
                class="tw-text-sm tw-leading-6 tw-text-gray-500 dark:tw-text-gray-400"
            >
                <div class="tw-mb-4 dark:tw-text-gray-200 tw-prose tw-prose-sm">
                    <div v-html="formattedBody"></div>

                    <button
                        type="button"
                        v-if="comment_longer_than_trancate_value"
                        @click="showing_full_text = !showing_full_text"
                        class="tw-bg-gray-100 tw-rounded tw-px-2 tw-py-0.5 tw-text-xs"
                    >
                        {{
                            showing_full_text
                                ? __("novaActivity.read_less")
                                : __("novaActivity.read_more")
                        }}
                    </button>
                </div>
            </div>
            <div class="tw-flex">
                <QuickReply
                    v-if="
                        field.use_quick_replies &&
                        (comment.has_quick_replies || show_comment)
                    "
                    :field="field"
                    :comment_id="comment.id"
                    :current_value="
                        comment.meta && comment.meta.quick_replies
                            ? comment.meta.quick_replies[
                                  'user_' + field.user.id
                              ]
                            : ''
                    "
                    action="update_quick_reply"
                    size="small"
                />
                <div
                    v-if="
                        field.use_quick_replies && comment.other_quick_replies
                    "
                    class="tw-flex tw--space-x-1 tw-mt-0 tw-overflow-hidden"
                >
                    <div
                        v-for="(
                            other_quick_reply, index
                        ) in comment.other_quick_replies"
                        class="tw-inline-block"
                        v-bind:key="index"
                    >
                        <div
                            v-if="other_quick_reply"
                            class="tw-flex tw-items-center tw-justify-center tw-h-5 tw-w-5 tw-rounded-full tw-ring-2 tw-ring-white"
                            :style="{
                                background:
                                    field.quick_replies[other_quick_reply]
                                        .background,
                                color:
                                    field.quick_replies[other_quick_reply]
                                        .color ?? '#fff',
                            }"
                        >
                            <Icon
                                class="tw-h-4 tw-w-4"
                                :name="
                                    field.quick_replies[other_quick_reply].icon
                                "
                                :solid="
                                    field.quick_replies[other_quick_reply]
                                        .solid_icon
                                "
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
    import QuickReply from "./QuickReply";
    import ActiviyActions from "./ActiviyActions";
    import ActivityStateIcons from "./ActivityStateIcons";
    import { Icon } from "laravel-nova-ui";

    export default {
        props: ["resourceName", "resourceId", "field", "comment", "hidden"],

        components: { QuickReply, ActiviyActions, ActivityStateIcons, Icon },

        data() {
            return {
                show_comment: false,
                showing_full_text: false,
                comment_longer_than_trancate_value: false,
            };
        },

        computed: {
            formattedBody() {
                if (
                    this.showing_full_text ||
                    !this.field.truncate_comments ||
                    !this.comment_longer_than_trancate_value
                ) {
                    return this.comment.comment;
                }

                return `${this.comment.comment.slice(0, this.field.truncate_comments).trim()}...`;
            },
        },

        created() {
            this.show_comment =
                this.comment.is_pinned || this.field.always_show_comments;

            if (this.comment.comment.length > this.field.truncate_comments) {
                this.comment_longer_than_trancate_value = true;
            }
        },

        methods: {
            toggleComment: function () {
                this.show_comment = !this.show_comment;
            },
        },
    };
</script>
