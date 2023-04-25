new Vue({
    el: "#root",
    data: {
        apiUrl: './controller.php',
        toDoList: [],

        isAddModalShow: false, // Todoを追加するモーダルを表示するフラグ
        inputNewTitle: "",
        inputNewContent: "",

        isEditModalShow: false, // Todoを編集するモーダルを表示するフラグ
        inputEditTitle: "",
        inputEditContent: "",

        inputSearch: "",

        currentClickIndex: "",
        currentPage: 1,
        toDoPerPage: 5,

        isDeleteModalShow: false, //削除時の確認メッセージモーダルを表示するフラグ
        deletedToDoLine: "", // 削除されたTodo行を一時特定するために存在する変数
        animateIndex: "",

        selectedOption: "CreatedAtAsc",
    },

    mounted() {
        this.getToDoList();
    },

    computed: {
        // todoListデータを1ページに表示する件数によって分割する
        paginatedToDo() {
            const start = (this.currentPage - 1) * this.toDoPerPage;
            const end = start + this.toDoPerPage;
            return this.toDoList.slice(start, end)
        },

        // 合計ページ数を計算する
        totalPages() {
            return Math.ceil(this.toDoList.length / this.toDoPerPage);
        },
    },

    methods: {
        // データを渡す
        sendDataToServer(url, data, deleteAnimate) {
            axios.post(url, data)
                .then(res => {
                    console.log(res.data);
                    deleteAnimate ? deleteAnimate() : this.getToDoList();
                }).catch(err => {
                    console.log(err);
                })
        },

        // データを受け取る
        getToDoList() {
            let apiUrl = this.apiUrl;
            if (this.selectedOption === "CreatedAtAsc") {
                apiUrl += '?action=getData'
            } else if (this.selectedOption === "UpDatedAtAsc") {
                apiUrl += '?action=sortBy' + this.selectedOption;
            }
            axios.get(apiUrl)
                .then((res) => {
                    this.toDoList = res.data;
                })
                .catch(err => {
                    console.log(err);
                })
        },

        // Todoの新規作成
        showAddModal() {
            this.isAddModalShow = true
        },
        addToDo() {
            if (this.inputNewTitle && this.inputNewTitle.length < 30 && this.inputNewContent) {
                let addData = {
                    title: this.inputNewTitle,
                    content: this.inputNewContent,
                }
                this.sendDataToServer(this.apiUrl + '?action=insertData', addData)
                this.isAddModalShow = false
            } else {
                alert("入力内容を確認してください。タイトルと内容を両方入力し、タイトルは30文字以内にしてください。")
            }

            // 最後のページを計算して移動する
            const finalPage = Math.ceil((this.toDoList.length + 1) / this.toDoPerPage)
            this.currentPage = finalPage
        },

        // 編集モーダルを表示
        showEditModal(item, index) {
            this.isEditModalShow = true
            this.inputEditTitle = item.title
            this.inputEditContent = item.content
            this.currentClickIndex = parseInt(this.paginatedToDo[index].ID)
        },
        // Todoの編集を確認する
        editToDo() {
            if (this.inputEditTitle && this.inputEditTitle.length < 30 && this.inputEditContent) {
                let updateData = {
                    title: this.inputEditTitle,
                    content: this.inputEditContent,
                    id: this.currentClickIndex
                }
                this.sendDataToServer(this.apiUrl + '?action=updateData', updateData)
                this.isEditModalShow = false
            } else {
                alert("入力内容を確認してください。タイトルと内容を両方入力し、タイトルは30文字以内にしてください。")
            }

            // 最後のページを計算して移動する
            if (this.selectedOption == "UpDatedAtAsc") {
                const finalPage = Math.ceil((this.toDoList.length + 1) / this.toDoPerPage)
                this.currentPage = finalPage
            }
        },

        // 削除モーダルを表示
        showDeleteModal(item, index) {
            this.isDeleteModalShow = true
            this.currentClickIndex = parseInt(this.paginatedToDo[index].ID) // 現在操作しているtodo項目のIDを特定する
            this.animateIndex = index
        },
        // 削除を確認する
        deleteToDo() {
            this.isDeleteModalShow = false

            let deleteAnimate = () => {
                this.deletedToDoLine = this.animateIndex;
                setTimeout(() => {
                    this.deletedToDoLine = ""
                    this.getToDoList()
                }, 700)
            }

            this.sendDataToServer(this.apiUrl + '?action=deleteData', {
                id: this.currentClickIndex
            }, deleteAnimate)
        },

        // 追加・編集・削除をキャンセルする
        cancelModal() {
            this.isAddModalShow = false
            this.isEditModalShow = false
            this.isDeleteModalShow = false
        },

        // タイトルでTodoを検索する
        searchToDo() {
            if (this.inputSearch) {
                this.currentPage = 1 // 検索したらcurrentPageも更新すること
                this.toDoList = this.toDoList.filter((item) => {
                    return item.title.includes(this.inputSearch)
                })
            } else {
                this.getToDoList()
            }
        },

        // 選択されたページに移動する
        setPage(pageNum) {
            this.currentPage = pageNum;
        },

    },

    filters: {
        // 日時データの処理
        dateFormate(value) {
            const date = new Date(value);
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            return year + "年" + month + "月" + day + "日";
        }
    }

})