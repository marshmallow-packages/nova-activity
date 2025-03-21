<template>
    <div class="tw-flex tw-items-start tw-gap-x-4">
        <div class="tw-flex-shrink-0 -tw-ml-[0.35rem]">
            <img
                v-if="field.user.avatar"
                :src="field.user.avatar"
                class="tw-inline-block tw-h-10 tw-w-10 tw-rounded-full"
            />
        </div>
        <div class="tw-min-w-0 tw-flex-1">
            <div class="tw-relative">
                <div class="tw-mb-2">
                    <input
                        name="focus_trap"
                        style="
                            position: absolute;
                            top: -50000px;
                            left: -50000px;
                        "
                    />
                    <model-select
                        @change="updateType"
                        class="block w-full form-control form-select form-select-multiple"
                        :options="field.types"
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
                    class="tw-overflow-hidden dark:tw-border-gray-700 dark:tw-ring-gray-700 dark:focus:tw-bg-gray-900 dark:tw-bg-gray-900 tw-rounded-lg trix-editor tw-shadow-sm tw-ring-1 tw-ring-inset tw-ring-gray-300"
                >
                    <Trix
                        v-if="field.use_comments"
                        :id="`${commentFieldId}`"
                        name="comment"
                        :value="comment"
                        @file-added="handleFileAdded"
                        @file-removed="handleFileRemoved"
                        :with-files="false"
                        :placeholder="__('novaActivity.comment_placeholder')"
                        class="tw-block tw-ml-px tw-pl-2 tw-w-[99%] mx-auto tw-resize-none tw-border-0 tw-outline-none tw-bg-transparent tw-py-1.5 tw-text-gray-900 placeholder:tw-text-gray-400 dark:text-gray-400 sm:tw-text-sm sm:tw-leading-6 focus:tw-outline-none"
                    />

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
                                    class="tw-w-4 focus:tw-outline-none tw-bg-transparent dark:[color-scheme:dark]"
                                    placeholder="Select date"
                                />
                                <span class="tw-text-xs">
                                    {{ parsedDate() }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="tw-flex-shrink-0">
                        <Button @click="submitComment" type="button">
                            {{ __("novaActivity.post_comment") }}
                        </Button>
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
    import { Icon, Button } from "laravel-nova-ui";
    import { FormField, HandlesValidationErrors } from "laravel-nova";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ["resourceName", "field", "resourceId", "commentFieldId"],

        components: { QuickReply, ActivityHistory, ModelSelect, Button },

        watch: {
            type: function (new_value, old_value) {
                this.submitChangeToParent("type", new_value);
                let type_label =
                    this.field.types[new_value] === undefined
                        ? ""
                        : this.field.types[new_value];
                this.submitChangeToParent("type_label", type_label);
            },
            date: function (new_value, old_value) {
                this.submitChangeToParent("date", new_value);
            },
            quick_reply: function (new_value, old_value) {
                this.submitChangeToParent("quick_reply", new_value);
            },
        },

        data() {
            return {
                date: "",
                type: "",
                quick_reply: "",
                filToolsDisplay: "inherit",
            };
        },

        mounted() {
            if (this.field.mentions) {
                var tribute = new Tribute({
                    values: this.field.mentions,
                    selectClass: "tw-bg-gray-100",
                    itemClass: "tw-border-b last:tw-border-b-0",
                    containerClass:
                        "tw-bg-white tw-divide-y tw-rounded tw-border tw-divide-dashed",
                    selectTemplate: function (item) {
                        return "<strong>@" + item.original.value + "</strong>";
                    },

                    menuItemTemplate: function (item) {
                        return (
                            '<div class="tw-p-1 tw-cursor-pointer tw-block tw-group tw-flex-shrink-0"><div class="tw-flex tw-items-center"><div><img class="tw-inline-block tw-h-4 tw-w-4 tw-rounded-full" src="' +
                            item.original.avatar_url +
                            '" alt=""></div><div class="tw-ml-3"><p class="tw-text-sm tw-font-medium tw-text-gray-700 group-hover:tw-text-gray-900">' +
                            item.string +
                            "</p></div></div></div>"
                        );
                    },
                });
                tribute.attach(document.getElementById(this.commentFieldId));
                var editor = document.getElementById(
                    this.commentFieldId
                ).editor;
                if (editor != null) {
                    if (
                        editor.composition.delegate.inputController.events !=
                        null
                    ) {
                        editor.composition.delegate.inputController.events.keypress =
                            function () {};
                        editor.composition.delegate.inputController.events.keydown =
                            function () {};
                    }
                }
            }
        },
        created() {
            this.date = this.moment(new Date()).format("YYYY-MM-DD");
            this.filToolsDisplay = this.field.use_file_uploads
                ? "inherit"
                : "none";
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
                    .format(this.field.js_date_format);
            },

            openDatePicker() {
                const elem = this.$refs.datePicker;
                elem.focus();
                elem.showPicker();
            },

            setQuickReply(quick_reply_key) {
                this.quick_reply = quick_reply_key;
            },

            submitChangeToParent(key, value) {
                Nova.$emit("novaActivitySetFormFieldValue", {
                    key: key,
                    value: value,
                });
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
                        ? document.getElementById(this.commentFieldId).value
                        : ""
                );
                formData.append("type", this.type);
                formData.append(
                    "mentions",
                    JSON.stringify(this.field.mentions)
                );

                let type_label = "";
                for (const [key] of Object.entries(this.field.types)) {
                    if (this.field.types[key].value == this.type) {
                        type_label = this.field.types[key].text;
                    }
                }

                formData.append("type_label", type_label);
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
                    document
                        .getElementById(this.commentFieldId)
                        .editor.loadHTML("");
                }
            },
        },
    };
</script>

<style>
    .trix-button-group.trix-button-group--file-tools {
        display: v-bind("filToolsDisplay");
    }
</style>
