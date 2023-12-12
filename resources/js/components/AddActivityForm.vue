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
                        :placeholder="
                            __('novaActivity.select_comment_type_placeholder')
                        "
                    >
                    </model-select>
                </div>

                <div
                    class="tw-overflow-hidden tw-rounded-lg tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300"
                >
                    <trix-editor
                        v-if="field.use_comments"
                        rows="3"
                        name="comment"
                        id="comment"
                        class="tw-block tw-pl-2 tw-w-full tw-resize-none tw-border-0 tw-outline-none tw-bg-transparent tw-py-1.5 tw-text-gray-900 placeholder:tw-text-gray-400 sm:tw-text-sm sm:tw-leading-6"
                        :placeholder="__('novaActivity.comment_placeholder')"
                    ></trix-editor>

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
                        <div class="tw-flex tw-items-center">
                            <QuickReply
                                :field="field"
                                action="new_comment"
                                ref="createQuickReply"
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
                        <DefaultButton @click="submitComment" type="button">
                            {{ __("novaActivity.post_comment") }}
                        </DefaultButton>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import Tribute from "tributejs";
    import QuickReply from "./QuickReply";
    import { ModelSelect } from "vue-search-select";
    import ActivityHistory from "./ActivityHistory";
    import "vue-search-select/dist/VueSearchSelect.css";
    import { FormField, HandlesValidationErrors } from "laravel-nova";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ["resourceName", "field", "resourceId"],

        components: { QuickReply, ActivityHistory, ModelSelect },

        data() {
            return {
                date: "",
                type: "",
                quick_reply: "",
                comment_types: [],
            };
        },

        mounted() {
            if (this.field.mentions) {
                var tribute = new Tribute({
                    values: this.field.mentions,
                    selectTemplate: function (item) {
                        return "<strong>@" + item.original.value + "</strong>";
                    },
                    menuItemTemplate: function (item) {
                        return (
                            '<img src="' +
                            item.original.avatar_url +
                            '">' +
                            item.string
                        );
                    },
                });
                tribute.attach(document.getElementById("comment"));
                var editor = document.getElementById("comment").editor;
                if (editor != null) {
                    editor.composition.delegate.inputController.events.keypress =
                        function () {};
                    editor.composition.delegate.inputController.events.keydown =
                        function () {};
                }
            }
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

                return moment(this.date)
                    .locale(this.field.locale)
                    .format(this.field.date_format);
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
                formData.append(
                    "comment",
                    this.field.use_comments
                        ? document.getElementById("comment").value
                        : ""
                );
                formData.append("type", this.type);
                formData.append("mentions", JSON.stringify(this.field.mentions));
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
                this.$refs.createQuickReply.reset();
                this.date = this.moment(new Date()).format("YYYY-MM-DD");

                if (this.field.use_comments) {
                    document.getElementById("comment").editor.loadHTML("");
                }
            },
        },
    };
</script>
