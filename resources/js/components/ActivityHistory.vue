<template>
    <div
        v-if="comment_history && comment_history.length"
        class="tw-mb-6 tw-mt-2"
    >
        <h2
            v-if="field.activity_title"
            class="tw-text-sm tw-font-semibold tw-leading-6 dark:text-gray-400 tw-text-gray-900 tw-mb-6"
        >
            {{ field.activity_title }}
        </h2>

        <div
            class="inline-flex items-center px-3 text-gray-500 bg-white border-gray-300 tw-border-b tw-justify-center tw-w-full focus:outline-none focus:ring dark:border-gray-700 hover:border-gray-500 active:border-gray-400 dark:hover:tw-border-gray-500 dark:active:tw-border-gray-600 tw-group dark:bg-transparent dark:text-gray-400 h-9 shrink-0 tw-cursor-pointer"
            v-if="field.limit && comment_history.length > field.limit"
            @click="show_all = !show_all"
        >
            <div v-if="!show_all" class="mr-1 group-hover:tw-font-semibold">
                <span class="tw-text-sm">
                    {{ __("novaActivity.view_all_activity") }}
                </span>
                <span
                    class="tw-text-xs tw-ml-0.5 tw-cursor-pointer dark:tw-text-gray-500 tw-text-gray-400"
                    >({{
                        __("novaActivity.count_more", {
                            count: comment_history.length - field.limit,
                        })
                    }})</span
                >
            </div>

            <div v-if="show_all" class="mr-1">
                {{ __("novaActivity.only_show_latests") }}
            </div>
            <CollapseButton :collapsed="show_all" />
        </div>
        <ul role="list" class="tw-space-y-6 js-history-list">
            <template v-for="(comment_history_item, index) in comment_history">
                <ActivityWithoutComment
                    v-bind:key="comment_history_item.id"
                    v-if="
                        !comment_history_item.comment ||
                        comment_history_item.is_hidden
                    "
                    :hidden="
                        field.limit &&
                        !show_all &&
                        index < comment_history.length - field.limit
                    "
                    :comment="comment_history_item"
                    :resourceName="resourceName"
                    :resourceId="resourceId"
                    :field="field"
                />

                <ActivityWithComment
                    v-bind:key="comment_history_item.id"
                    v-if="
                        comment_history_item.comment &&
                        !comment_history_item.is_hidden
                    "
                    :hidden="
                        field.limit &&
                        !show_all &&
                        index < comment_history.length - field.limit
                    "
                    :comment="comment_history_item"
                    :resourceName="resourceName"
                    :resourceId="resourceId"
                    :field="field"
                />
            </template>
        </ul>
    </div>
</template>

<script>
    import ActivityWithComment from "./ActivityWithComment";
    import ActivityWithoutComment from "./ActivityWithoutComment";

    export default {
        props: ["resourceName", "resourceId", "field"],

        components: { ActivityWithComment, ActivityWithoutComment },

        data() {
            return {
                show_all: false,
                comment_history: [],
            };
        },

        created() {
            this.loadCommentHistory();
        },

        methods: {
            loadCommentHistory() {
                let self = this;
                Nova.request()
                    .get(
                        `/nova-vendor/nova-activity/${this.resourceName}/${this.resourceId}/get-activity`
                    )
                    .then(
                        (response) => {
                            self.comment_history = response.data.data;
                        },
                        (response) => {
                            Nova.error(response);
                        }
                    )
                    .finally(() => {
                        //
                    });
            },
        },
    };
</script>
