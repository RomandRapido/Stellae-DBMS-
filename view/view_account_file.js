likeStatusOfUser = false;
isLogin = false;
validPost = false;
function initializeLoginStatus(loginConfirm) {
  if (loginConfirm) {
    isLogin = true;
  }
}

function postContents(postId) {
  let papersContainer = document.getElementById("all_paper_pages");
  console.log(papersContainer);
  const formData = new FormData();
  // console.log(Number.isInteger(postId));
  formData.append("action", "get_word_contents");
  formData.append("postId", postId);
  // console.log(formData.get('postId'));
  const endpoint = "logistic_man_arrays.php";

  fetch(endpoint, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((result) => {
      // console.log(result);
      parsedResult = JSON.parse(result);
      likeStatusOfUser = parsedResult.initial_liked;
      display_content(parsedResult, papersContainer);
      validPost = true;
    })
    .catch((error) => {
      var notFoundPost = {
        post_id: "POST_NOT_FOUND",
        title: "Post Not Found",
        author_name: "N/A",
        published_date: "N/A",
        content: "The requested post was not found.",
        interests: [],
        initial_liked: false,
        likes: 0,
        image_dir: "imgDirectory/default.jpg",
        author_id: null,
      };
      var errorObject = JSON.stringify(notFoundPost);
      display_content(JSON.parse(errorObject), papersContainer);
      throw new Error("Error retrieving post contents", error);
    });
}

function display_content(array_content, put_content_here) {
  let create_paper = document.createElement("div");
  create_paper.classList.add("paper1");
  create_paper.innerHTML = array_content.content;
  console.log(put_content_here);
  put_content_here.appendChild(create_paper);
  let top_view_paper = document.getElementById("account_profile_info");
  top_view_paper.innerHTML = `<div class='profile_top_top'>
  								<a href="../profile/account_view.php?UserId=${array_content.author_id}">
									<img class='profile_image_picture' src= "../${array_content.image_dir}">
									</a>
									<p class='top_texts'>${array_content.author_name}</p>
									
									<p class='top_texts date_content'>${array_content.published_date}</p>
								</div>
								<p class='top_texts'>${array_content.title}</p>
								<div id = 'button_containers'>
								</div>
		`;
  let tags_div = document.getElementById("button_containers");
  array_content.interests.forEach(function (tag) {
    let button = document.createElement("button");
    button.innerHTML = tag;
    button.classList.add("interests_available");
    button.classList.add("turn_flame");
    button.id = tag;
    button.onclick = function () {
      let button_interest = document.getElementById(tag);
      button_interest.classList.toggle("interests_available");
    };
    tags_div.appendChild(button);
  });
}

function comments_section(commentArray, postId) {
  let container_div_comment_n_rate = document.getElementById("rating_section");
  container_div_comment_n_rate.innerHTML = "";

  commentArray.forEach(function (comment) {
    let new_comment = document.createElement("div");
    new_comment.classList.add("comment_div");
    innner_html = `
					<div class="author_date">
						<a href="../profile/account_view.php?UserId=${comment.author_id}">
						<p class="comment_author">${comment.author}</p>
						</a>
						<p class="comment_date">${comment.date}</p>
					</div>
					<div class="comment_text">
						<p>${comment.content}</p>
					</div>
					`;
    new_comment.innerHTML = innner_html;
    container_div_comment_n_rate.appendChild(new_comment);
  });
  let commetation_div = document.getElementById("partial_comment_sec");
  commetation_div.innerHTML = `<form action='addComment.php' method='GET'>
									<fieldset>
									<input type="hidden" name="postId" value="${postId}">
										<textarea name="content" class="type_comment"></textarea>
										<input type="submit">
									</fieldset>
								</form>`;
}
function enable_dropfeature(div_ID, postId) {
  if (validPost) {
    let commetation_div = document.getElementById("partial_comment_sec");
    commetation_div.innerHTML = "";

    let container_div_comment_n_rate = document.getElementById(div_ID);
    container_div_comment_n_rate.innerHTML = "";
    let main_rating_forms = document.createElement("form");
    main_rating_forms.action = "addGrade.php";
    main_rating_forms.method = "POST";

    let hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "postId";
    hiddenInput.value = postId;
    main_rating_forms.appendChild(hiddenInput);

    let form_div = document.createElement("fieldset");
    form_div.classList.add("form_div");

    let header_rating = [
      "Component",
      "4 points",
      "3 points",
      "2 points",
      "1 point",
    ];
    header_rating.forEach(function (header) {
      let new_label = document.createElement("label");
      new_label.classList.add("top_part_rate");
      new_label.innerHTML = header;
      form_div.appendChild(new_label);
      // console.log(new_label);
    });

    let forms_criteria = [
      [
        "Quality and Clarity of thoughts, Development of ideas",
        "Answers reflect depth and complexity of thought Explores ideas vigorously, supports points fully using a balance of subjective and objective evidence, reasons effectively making useful distinctions.",
        "Answers reflect simplicity or repetitive of thoughts.Supports most ideas with effective examples, references, and details, makes key distinctions.",
        "Demonstrates confused or conflicting thoughts.Presents ideas in general terms, support for ideas is inconsistent, some distinctions need clarification, reasoning unclear.",
        "Unfocused, illogical,or incoherent thoughts.Most ideas unsupported, confusion between personal and external evidence, reasoning flawed.",
      ],
      [
        "Connection and Response",
        "Output demonstrates accurate and complete understanding of the aim of the activity.Incorporates knowledge and understanding of concepts and ideas from the lesson.",
        "Output is relatively aligned with the aim of the activity. Displays basic knowledge of the concept and ideas on the lesson.",
        "Output mostly deviates from the aim of the activity.Incorporates some information from the lesson but not in an overly thorough manner.",
        "Fails to address the aim of the activity and/or demonstrates an inadequate or partial grasp of the lesson.",
      ],
      [
        "Organization and Development of Ideas",
        "The content is organized logically with fluid transitions to capture and hold the audience’s attention throughout the entire presentation.",
        "The organization of the content is congruent; transitions are evident.",
        "The organization of the content is acceptable but could still be improved.",
        "The content lacks organization: transitions are abrupt and distracting.",
      ],
      [
        "Spelling and Grammar",
        "The writing is essential error-free in terms of spelling and grammar.",
        "While there may be minor errors, the writing follows normal conventions of spelling and grammar throughout and has been carefully proofread.",
        "Frequent errors in spelling and grammar distract the reader.",
        "Writing contains numerous errors in spelling and grammar which interfere with comprehension.",
      ],
      [
        "Reference and Citation",
        "All sources are accurately cited in the APA 7th Ed. format both in the text and on the reference lists.",
        "Most sources are accurately cited, but a few are not in the right format. Some sources lack credibility.",
        "Some sources are accurately documented, many are not in the right format or lack credibility.",
        "Format is incorrect for all sources.",
      ],
    ];
    forms_criteria.forEach(function (criterion) {
      let new_label = document.createElement("label");
      new_label.innerHTML = criterion[0];
      form_div.appendChild(new_label);
      criterion.forEach(function (criteria) {
        if (criterion.indexOf(criteria) > 0) {
          let form_div_sub = document.createElement("fieldset");
          form_div.classList.add("form_div_sub");
          form_div_sub.innerHTML = `
										<input type="radio" name="points${forms_criteria.indexOf(criterion)}" value=${
            criterion.length - criterion.indexOf(criteria)
          }>
										<label class="description_criteria">${criteria}</label>`;
          form_div.appendChild(form_div_sub);
        }
      });
    });

    let comment_div = document.createElement("fieldset");

    let comment_label = document.createElement("label");
    comment_label.innerHTML =
      "Comments, suggestions, and points for improvements";
    comment_div.appendChild(comment_label);

    let comment_div_sub = document.createElement("fieldset");
    comment_div_sub.innerHTML = `
			<br>
			<fieldset>
				<textarea name="comment" class="type_comment"></textarea>
			</fieldset>`;
    comment_div.appendChild(comment_div_sub);

    let form_div_sub = document.createElement("fieldset");
    form_div_sub.innerHTML = `<input type="submit" class="submit_btn_forms" value='Submit'>`;
    comment_div.appendChild(form_div_sub);

    main_rating_forms.appendChild(form_div);
    main_rating_forms.appendChild(comment_div);
    container_div_comment_n_rate.appendChild(main_rating_forms);
  } else {
    alert("Invalid post");
  }
}

function initializeLikedStatus(initialLiked) {
  if (isLogin) {
    if (initialLiked) {
      const likeButton = document.getElementById(`buttonLike`);
      likeButton.classList.toggle("liked");
    }
  }
}

function toggleLike(postId) {
  if (validPost) {
    if (isLogin) {
      const likeButton = document.getElementById(`buttonLike`);
      likeStatusOfUser = !likeStatusOfUser;

      likeButton.classList.toggle("liked", likeStatusOfUser);

      const updateLikeEndpoint = "../feed/like_logic.php";
      const formData = new FormData();
      formData.append("post_id", postId);

      fetch(updateLikeEndpoint, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.text();
        })
        .then((data) => {
          console.log("Like status updated on the server:", data);
        })
        .catch((error) => {
          console.error("Error updating like status:", error);
        });
    } else {
      alert("Please Log in for this feature to be available");
    }
  } else {
    alert("Invalid post");
  }
}
initializeLikedStatus(likeStatusOfUser);

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("rating_section");

  form.addEventListener("submit", function (event) {
    const fieldsets = document.querySelectorAll(".form_div_sub");

    let allChecked = true;

    fieldsets.forEach(function (fieldset) {
      const radioChecked = fieldset.querySelector(
        'input[type="radio"]:checked'
      );

      if (!radioChecked) {
        allChecked = false;
      }
    });

    if (!allChecked) {
      event.preventDefault();
      alert("Please select a value for each criterion.");
    }
  });
});

function initializeCommentSection(postId) {
  if (validPost) {
    if (isLogin) {
      const getCommentsEndpoint = "logistic_man_arrays.php";
      const formData = new FormData();
      formData.append("postId", postId);
      formData.append("action", "get_comments");

      fetch(getCommentsEndpoint, {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return response.text();
        })
        .then((result) => {
          parsedResult = JSON.parse(result);
          // console.log(parsedResult);
          comments_section(parsedResult, postId);
        })
        .catch((error) => {
          console.error("Error updating like status:", error);
        });
    } else {
      alert("Please Log in for this feature to be available");
    }
  } else {
    // alert('Invalid post');
  }
}

function redirectToPage(index, UserId) {
  if (index == 0) {
    window.location.href = "../feed/home_page.php";
  } else if (index == 1) {
    window.location.href = `../Profile/account_view.php?UserId=${UserId}`;
  } else if (index == 2) {
    window.location.href = "../postCreation/post_creation.php";
  } else {
    var userConfirmed = confirm("Do you want to proceed with logging out?");
    if (userConfirmed) {
      window.location.href = "../logout.php";
    }
  }
}
