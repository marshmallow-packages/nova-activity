<template>
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
                <div class="tw-mb-2">
                    <model-select
                        :options="comment_types"
                        v-model="type"
                        id="type"
                        name="type"
                        placeholder="Select your comment type..."
                    >
                    </model-select>
                </div>

                <div
                    class="tw-overflow-hidden tw-rounded-lg tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300"
                >
                    <textarea
                        v-model="comment"
                        rows="3"
                        name="comment"
                        id="comment"
                        class="tw-block tw-pl-2 tw-w-full tw-resize-none tw-border-0 tw-outline-none tw-bg-transparent tw-py-1.5 tw-text-gray-900 dark:text-gray-400 placeholder:tw-text-gray-400 sm:tw-text-sm sm:tw-leading-6"
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
                            <QuickReply :field="field" action="new_comment" />
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
                        <DefaultButton @click="submitComment" type="button">
                            Post
                        </DefaultButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import "vue-search-select/dist/VueSearchSelect.css";
    import moment from "moment";
    import { ModelSelect } from "vue-search-select";
    import QuickReply from "./QuickReply";
    import ActivityHistory from "./ActivityHistory";
    import { FormField, HandlesValidationErrors } from "laravel-nova";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ["resourceName", "field", "resourceId"],

        components: { QuickReply, ActivityHistory, ModelSelect },

        data() {
            return {
                date: "",
                type: "",
                comment: "",
                quick_reply: "",
                show_comments: [],
                comment_types: [],
                item: "",
                input: "",
            };
        },

        created() {
            this.date = this.moment(new Date()).format("YYYY-MM-DD");

            for (const comment_type in this.field.types) {
                this.comment_types.push({
                    value: comment_type,
                    text: this.field.types[comment_type],
                });
            }
        },

        methods: {
            moment: function () {
                return moment();
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
                formData.append(
                    "type_label",
                    this.field.types[this.type] === undefined
                        ? ""
                        : this.field.types[this.type]
                );
                formData.append("quick_reply", this.quick_reply);

                return Nova.request()
                    .post(
                        `/nova-vendor/nova-activity/${this.resourceName}/${this.resourceId}`,
                        formData
                    )
                    .then(
                        (response) => {
                            if (response.data.success) {
                                Nova.success(response.data.message);
                                self.$parent.$refs.novaActivityHistory.loadCommentHistory();
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
        },
    };
</script>
