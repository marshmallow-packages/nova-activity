import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-comments', IndexField)
  app.component('detail-comments', DetailField)
  app.component('form-comments', FormField)
})
