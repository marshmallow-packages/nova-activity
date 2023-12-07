<template>
    <div>
        <NovaCommentHistory
            ref="novaCommentHistory"
            :field="field"
            :resourceName="resourceName"
            :resourceId="resourceId"
        />
        <div class="tw-flex tw-items-start tw-space-x-4">
            <div class="tw-flex-shrink-0">
                <img
                    v-if="field.user.avatar"
                    :src="field.user.avatar"
                    class="tw-inline-block tw-h-10 tw-w-10 tw-rounded-full"
                />
            </div>
            <div class="tw-min-w-0 tw-flex-1">
                <div class="tw-relative">
                    <div>
                        <select
                            v-if="field.types"
                            v-model="type"
                            id="type"
                            name="type"
                            class="tw-mb-2 tw-block tw-w-full tw-rounded-md tw-border-0 tw-py-1.5 tw-pl-3 tw-pr-10 tw-text-gray-900 tw-ring-1 tw-ring-inset tw-ring-gray-300 focus:tw-ring-2 focus:tw-ring-indigo-600 sm:tw-text-sm sm:tw-leading-6"
                        >
                            <option value="">
                                Choose your comment type...
                            </option>
                            <option
                                :value="type_id"
                                v-for="(type, type_id) in field.types"
                                v-bind:key="type_id"
                            >
                                {{ type }}
                            </option>
                        </select>
                    </div>

                    <div
                        class="tw-overflow-hidden tw-rounded-lg tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300"
                    >
                        <textarea
                            v-model="comment"
                            rows="3"
                            name="comment"
                            id="comment"
                            class="tw-block tw-pl-2 tw-w-full tw-resize-none tw-border-0 tw-outline-none tw-bg-transparent tw-py-1.5 tw-text-gray-900 placeholder:tw-text-gray-400 sm:tw-text-sm sm:tw-leading-6"
                            placeholder="Add your comment... "
                        ></textarea>
                        <div class="tw-py-2" aria-hidden="true">
                            <div class="tw-py-px">
                                <div class="tw-h-9"></div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="tw-absolute tw-inset-x-0 tw-bottom-0 tw-flex tw-justify-between tw-py-2 tw-pl-3 tw-pr-2"
                    >
                        <div class="tw-flex tw-items-center tw-space-x-5">
                            <!-- <div class="tw-flex tw-items-center">Star</div> -->
                            <div class="tw-flex tw-items-center">
                                <QuickReply
                                    :field="field"
                                    action="new_comment"
                                />
                            </div>
                            <div class="tw-flex tw-items-center">
                                <div
                                    @click="openDatePicker()"
                                    class="tw-relative tw-max-w-sm tw-border tw-rounded tw-px-2 tw-cursor-pointer"
                                >
                                    <input
                                        ref="datePicker"
                                        v-model="date"
                                        type="date"
                                        class="tw-w-4 focus:tw-outline-none"
                                        placeholder="Select date"
                                    />
                                    <span class="tw-text-xs">
                                        {{ parsedDate() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="tw-flex-shrink-0">
                            <button
                                @click="submitComment"
                                type="button"
                                class="tw-inline-flex tw-items-center tw-rounded-md tw-bg-indigo-600 tw-px-3 tw-py-2 tw-text-sm tw-font-semibold tw-text-white tw-shadow-sm hover:tw-bg-indigo-500 focus-visible:tw-outline focus-visible:tw-outline-2 focus-visible:tw-outline-offset-2 focus-visible:tw-outline-indigo-600"
                            >
                                Post
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import QuickReply from "./QuickReply";
import NovaCommentHistory from "./NovaCommentHistory";
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ["resourceName", "field", "resourceId"],

    components: { QuickReply, NovaCommentHistory },

    data() {
        return {
            date: "",
            type: "",
            comment: "",
            quick_reply: "",
            show_comments: [],
        };
    },

    created() {
        this.date = this.moment(new Date()).format("YYYY-MM-DD");
    },

    methods: {
        moment: function () {
            return moment();
        },
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
        parsedDate() {
            if (!this.date) {
                return "";
            }

            return moment(this.date).format("Do MMM, YYYY");
        },

        openDatePicker() {
            const elem = this.$refs.datePicker;
            elem.focus();
            elem.showPicker();
        },

        setQuickReply(quick_reply_key) {
            this.quick_reply = quick_reply_key;
        },

        submitComment() {
            let self = this;
            let formData = new FormData();
            formData.append("resource_name", this.resourceName);
            formData.append("resource_id", this.resourceId);
            formData.append("date", this.date);
            formData.append("comment", this.comment);
            formData.append("type", this.type);
            formData.append("type_label", this.field.types[this.type]);
            formData.append("quick_reply", this.quick_reply);

            return Nova.request()
                .post(
                    `/nova-vendor/nova-comments/${this.resourceName}/${this.resourceId}`,
                    formData
                )
                .then(
                    (response) => {
                        if (response.data.success) {
                            Nova.success(response.data.message);
                            self.$refs.novaCommentHistory.loadCommentHistory();
                            self.resetForm();
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

        resetForm() {
            this.type = "";
            this.comment = "";
            this.quick_reply = "";
            this.date = this.moment(new Date()).format("YYYY-MM-DD");
        },

        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || "";
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.fieldAttribute, this.value || "");
        },
    },
};
</script>
