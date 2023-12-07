<template>
    <div>
        <h2
            v-if="comment_history && comment_history.length"
            class="tw-text-sm tw-font-semibold tw-leading-6 tw-text-gray-900"
        >
            {{ field.activity_title }}
        </h2>
        <ul role="list" class="tw-mt-6 tw-space-y-6">
            <template
                v-for="comment_history_item in comment_history"
            >
                <li
                    v-bind:key="comment_history_item.id"
                    v-if="!comment_history_item.comment"
                    class="tw-relative tw-flex tw-gap-x-4"
                >
                    <div
                        class="tw-absolute tw-left-0 tw-top-0 tw-flex tw-w-6 tw-justify-center tw--bottom-6"
                    >
                        <div class="tw-w-px tw-bg-gray-200"></div>
                    </div>
                    <div
                        class="tw-relative tw-flex tw-h-6 tw-w-6 tw-flex-none tw-items-center tw-justify-center tw-bg-white"
                    >
                        <div
                            class="tw-h-1.5 tw-w-1.5 tw-rounded-full tw-bg-gray-100 tw-ring-1 tw-ring-gray-300"
                        ></div>
                    </div>
                    <p
                        class="tw-flex-auto tw-py-0.5 tw-text-xs tw-leading-5 tw-text-gray-500"
                    >
                        <span class="tw-font-medium tw-text-gray-900">{{
                            comment_history_item.user.name
                        }}</span>
                        {{ comment_history_item.type.label }}
                    </p>
                    <time
                        class="tw-flex-none tw-py-0.5 tw-text-xs tw-leading-5 tw-text-gray-500"
                    >
                        {{ comment_history_item.time_ago }}
                    </time>
                </li>
                <li
                    v-bind:key="comment_history_item.id"
                    v-if="comment_history_item.comment"
                    class="tw-relative tw-flex tw-gap-x-4"
                >
                    <div
                        class="tw-absolute tw-left-0 tw-top-0 tw-flex tw-w-6 tw-justify-center tw--bottom-6"
                    >
                        <div class="tw-w-px tw-bg-gray-200"></div>
                    </div>
                    <img
                        :src="comment_history_item.user.avatar"
                        alt=""
                        class="tw-relative tw-mt-3 tw-h-6 tw-w-6 tw-flex-none tw-rounded-full tw-bg-gray-50"
                    />
                    <div
                        class="tw-flex-auto tw-rounded-md tw-p-3 tw-ring-1 tw-ring-inset tw-ring-gray-200"
                    >
                        <div
                            @click="toggleComment(comment_history_item.id)"
                            class="tw-flex tw-cursor-pointer tw-justify-between tw-gap-x-4"
                        >
                            <div
                                class="tw-py-0.5 tw-text-xs tw-leading-5 tw-text-gray-500"
                            >
                                <span
                                    class="tw-font-medium tw-text-gray-900"
                                >
                                    {{ comment_history_item.user.name }}
                                </span>
                                {{ comment_history_item.type.label }}
                            </div>
                            <time
                                class="tw-flex-none tw-py-0.5 tw-text-xs tw-leading-5 tw-text-gray-500"
                            >
                                {{ comment_history_item.time_ago }}
                            </time>
                        </div>
                        <div
                            v-if="
                                show_comments.includes(
                                    comment_history_item.id
                                )
                            "
                            class="tw-text-sm tw-leading-6 tw-text-gray-500"
                        >
                            <p class="tw-mb-4" v-html="comment_history_item.comment"></p>
                        </div>
                        <div class="tw-flex">
                            <QuickReply
                                :field="field"
                                :comment_id="comment_history_item.id"
                                :current_value="comment_history_item.meta && comment_history_item.meta.quick_replies ? comment_history_item.meta.quick_replies['user_' + field.user.id] : ''"
                                action="update_quick_reply"
                                size="small"
                            />
                            <div v-if="comment_history_item.other_quick_replies" class="tw-flex tw--space-x-1 tw-mt-0 tw-overflow-hidden">
                                <div v-for="(other_quick_reply, index) in comment_history_item.other_quick_replies" class="tw-inline-block" v-bind:key="index">
                                    <div v-if="other_quick_reply" class="tw-flex tw-items-center tw-justify-center tw-h-5 tw-w-5 tw-rounded-full tw-ring-2 tw-ring-white" :style="{
                                            background:
                                                field.quick_replies[other_quick_reply].background,
                                            color:
                                                field.quick_replies[other_quick_reply].color ??
                                                '#fff',
                                        }">
                                        <Icon
                                            :width="12"
                                            :type="field.quick_replies[other_quick_reply].icon"
                                            :solid="
                                                field
                                                    .quick_replies[
                                                    other_quick_reply
                                                ].solid_icon
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </template>
        </ul>
    </div>
</template>

<script>

import QuickReply from "./QuickReply";
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "resourceId", "field"],

    components: { QuickReply },

    data() {
        return {
            comment_history: [],
            show_comments: [],
        };
    },

    created() {
        this.loadCommentHistory();
    },

    methods: {

        toggleComment: function (comment_id) {
            if (this.show_comments.includes(comment_id)) {
                var index = this.show_comments.indexOf(comment_id);
                if (index !== -1) {
                    this.show_comments.splice(index, 1);
                }
            } else {
                this.show_comments.push(comment_id);
            }
        },

        loadCommentHistory() {
            let self = this;
            Nova.request()
                .get(
                    `/nova-vendor/nova-comments/${this.resourceName}/${this.resourceId}/get-comments`
                )
                .then(
                    (response) => {
                        self.comment_history = response.data.data;
                        if (response.data.success) {
                            Nova.success(response.data.message);
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

         /*
         * Set the initial, internal value for the field.
         */
        // setInitialValue() {
        //     this.value = this.field.value || "";
        // },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        // fill(formData) {
        //     formData.append(this.fieldAttribute, this.value || "");
        // },
    },
};
</script>
